<?php
/**
 * Nexo Framework - Core Bootstrap File
 * 
 * The revolutionary PHP framework for enterprise applications
 * Built for performance, scalability, and multi-project architecture
 * 
 * @author    Nexo Framework Development Team
 * @copyright 2024 Nexo Framework
 * @license   MIT License
 * @version   1.0.0
 */

@session_start();

$GLOBALS['current_directory'] = false;
$GLOBALS['route_info'] = array();
$GLOBALS['route_view'] = false;
$GLOBALS['route_group'] = false;
$GLOBALS['route_name'] = array();

require_once(APPPATH . 'configs/autoload.php');

#for multiple module session and cookie management
if (file_exists(APPPATH . 'configs/saggregator.php')) {
    require_once(APPPATH . 'configs/saggregator.php');
    if (isset($sgroup) && is_array($sgroup)) {
        if (is_array(current($sgroup))) {
            $group = [];
            foreach ($sgroup as $key => $rows) {
                $igroup = [];
                foreach ($rows as $row) {
                    $igroup[$row] = $row;
                }
                $group[$key] = $igroup;
            }
            $sgroup = $group;
            $GLOBALS['sgroup'] = $sgroup;
        }
    }
}

require_once(SYSTEMPATH . 'bind/uri.php');

require_once(SYSTEMPATH . 'bind/route.php');

require_once(SYSTEMPATH . 'bind/hmvc.php');

require_once(SYSTEMPATH . 'bind/gloader.php');

require_once(SYSTEMPATH . 'bind/gcontroller.php');

//require_once(SYSTEMPATH . 'bind/glibrary.php');
//require_once(SYSTEMPATH . 'bind/ghelper.php');

require_once(SYSTEMPATH . 'bind/gmiddleware.php');

require_once(SYSTEMPATH . 'drivers/dbselectionmodels.php');

require_once(SYSTEMPATH . 'bind/gconfig-constants.php');

require_once(SYSTEMPATH . 'bind/nexo-exceptions.php');

class Nexo {

    private $obj;
    public $uri;
    public $module;
    public $controller;
    public $method;
    public $params;
    public $classInfo = array();
    public $urlInfo = array();
    public $routingInfo = array();
    public $current_directory = "";
    private $middleware;
    private $stime;

    function __construct() {
        #get uri object
        $this->obj = array();
        $this->params = array();
        
        try {
            $this->run();
        } catch (NexoException $e) {
            $this->handleNexoException($e);
        } catch (Exception $e) {
            $this->handleGenericException($e);
        }
    }

    public function run() {
        $this->routingInfo = isset($GLOBALS['routingInfo']) ? $GLOBALS['routingInfo'] : array();
        $this->controller = isset($GLOBALS['controller']) ? $GLOBALS['controller'] : '';
        $this->method = isset($this->routingInfo['method']) ? $this->routingInfo['method'] : '';
        $this->params = isset($this->routingInfo['params']) ? $this->routingInfo['params'] : array();
        $this->middleware = isset($this->routingInfo['middleware']) ? current($this->routingInfo['middleware']) : array();

        #make first letter of directory  to uppercase
        $this->controller = $this->array_walk($this->controller);

        #check if controller exists other throw exception
        if (!file_exists($this->controller)) {
            throw new ControllerNotFoundException($this->controller);
        }


        #include controllers auxiliary files as like Interfaces , Abstracts and Traits
        $controller_path = str_replace("//", "/", $GLOBALS['current_directory'] . "/controllers/");
        $controller = explode("/", $this->controller);
        $controller = end($controller);
        $controller_arr[] = $controller;
        if (is_array($controller_arr)) {
            $this->_include_controllers_files($controller_arr, $controller_path);
        }

        /*
         * For database module_name detection for modular database adding facilities
         */
        $GLOBALS['module-name'] = isset($this->routingInfo['module']) ? current($this->routingInfo['module']) : 'default';
        /*
         *
         * inclucde controller now
         *
         */

        require_once ($this->controller);

        $route_params = $this->_reset_optional_params($this->routingInfo['params']);

        if (!$fun_info = $this->_get_filter_function_name()) {
            throw new RouteNotFoundException($this->uri ?? 'unknown', $this->method ?? []);
        }

        /*
         *
         * inclucde middleware now if exists and run those class method
         *
         */

        $middleware_path = str_replace("//", "/", $GLOBALS['current_directory'] . "/middlewares/");
        if (is_array($this->middleware)) {
            $this->_include_middlewares_files($this->middleware, $middleware_path);
        }

        #now go ahead to call controller and its function and put parameters if available
        #initialize controller object
        $clsName = explode("/", str_replace(".php", "", $this->controller));
        $className = end($clsName);
        $clsObj = new $className;

        $GLOBALS['route_view'] = str_replace("//", "/", $GLOBALS['current_directory'] . "/views/" . $className);

        #calling function of controller
        $method_name = key($fun_info);

        if (count(current($fun_info))) {
            $fun_params = array();
            foreach ($fun_info[$method_name] as $key => $value) {
                $fun_params[$key] = isset($route_params[$value]) ? $route_params[$value] : '';
            }
            #now fill optional value
            foreach ($route_params as $key => $value) {
                if (is_numeric($key)) {
                    foreach ($fun_info[$method_name] as $k => $v) {
                        if (!is_numeric($k)) {
                            $fun_params[$k] = $value;
                        }
                    }
                }
            }
            call_user_func_array(array($clsObj, $method_name), $fun_params);
        } else {
            call_user_func_array(array($clsObj, $method_name), array());
        }
    }

    private function _directory_list($dir_path) {
        $file_list = array();
        $route_directory = $dir_path;
        if (!is_dir($route_directory)) {
            throw new DirectoryNotFoundException($route_directory);
        }
        if ($handle = opendir($route_directory)) {
            while (false !== ($file = readdir($handle))) {
                if ($file != "." && $file != "..") {
                    $ext = explode(".", $file);
                    $ext = strtolower(end($ext));
                    if ($ext === 'php') {
                        $file_list[] = $this->array_walk($route_directory . '/' . $file);
                    }
                }
            }
            closedir($handle);
        }
        return $file_list;
    }

    private function _include_controllers_files($_controllers, $controllers_path) {
        foreach ($_controllers as $controller) {
            $controller_directory = ucfirst(strtolower($controllers_path));

            $controller_file = ucfirst(strtolower($controller));

            #create an array for Interface, Traits
            $Icontroller_file_path[] = $this->array_walk($controller_directory) . 'i' . $controller_file;
            $Tcontroller_file_path[] = $this->array_walk($controller_directory) . 't' . $controller_file;
            $Acontroller_file_path[] = $this->array_walk($controller_directory) . 'a' . $controller_file;

            #directory for interface and Traits
            $Icontroller_file_path_dir = $this->array_walk($controller_directory . 'interfaces');
            $Tcontroller_file_path_dir = $this->array_walk($controller_directory . 'traits');
            $Acontroller_file_path_dir = $this->array_walk($controller_directory . 'abstracts');

            if (is_dir($Icontroller_file_path_dir)) {
                $list = $this->_directory_list($Icontroller_file_path_dir);
                $Icontroller_file_path = array_merge($Icontroller_file_path, $list);
            }
            if (is_dir($Tcontroller_file_path_dir)) {
                $list = $this->_directory_list($Tcontroller_file_path_dir);
                $Tcontroller_file_path = array_merge($Tcontroller_file_path, $list);
            }
            if (is_dir($Acontroller_file_path_dir)) {
                $list = $this->_directory_list($Acontroller_file_path_dir);
                $Acontroller_file_path = array_merge($Acontroller_file_path, $list);
            }
            #include interfaces file if exists
            foreach ($Icontroller_file_path as $interface) {
                if (file_exists($interface)) {
                    require_once ($interface);
                }
            }
            #include Traits file if exists
            foreach ($Tcontroller_file_path as $traits) {
                if (file_exists($traits)) {
                    require_once ($traits);
                }
            }
            #include Abstract class
            foreach ($Acontroller_file_path as $abstracts) {
                if (file_exists($abstracts)) {
                    require_once ($abstracts);
                }
            }
        }
    }

    private function _include_middlewares_files($_middlewares, $middleware_path) {
        foreach ($_middlewares as $middleware) {
            $middleware_directory = ucfirst(strtolower($middleware));
            $middleware_file = ucfirst(strtolower($middleware));
            $middleware_file_path = $this->array_walk($middleware_path . $middleware_directory) . '/' . $middleware_file . ".php";

            #create an array for Interface, Traits, Abstracts
            $Imiddleware_file_path[] = $this->array_walk($middleware_path . $middleware_directory) . '/i' . $middleware_file . ".php";
            $Tmiddleware_file_path[] = $this->array_walk($middleware_path . $middleware_directory) . '/t' . $middleware_file . ".php";
            $Amiddleware_file_path[] = $this->array_walk($middleware_path . $middleware_directory) . '/a' . $middleware_file . ".php";

            #directory for interface and Traits
            $Imiddleware_file_path_dir = $this->array_walk($middleware_path . $middleware_directory . '/interfaces');
            $Tmiddleware_file_path_dir = $this->array_walk($middleware_path . $middleware_directory . '/traits');
            $Amiddleware_file_path_dir = $this->array_walk($middleware_path . $middleware_directory . '/abstracts');

            if (is_dir($Imiddleware_file_path_dir)) {
                $list = $this->_directory_list($Imiddleware_file_path_dir);
                $Imiddleware_file_path = array_merge($Imiddleware_file_path, $list);
            }
            if (is_dir($Tmiddleware_file_path_dir)) {
                $list = $this->_directory_list($Tmiddleware_file_path_dir);
                $Tmiddleware_file_path = array_merge($Tmiddleware_file_path, $list);
            }

            #include interfaces file if exists
            foreach ($Imiddleware_file_path as $interface) {
                if (file_exists($interface)) {
                    require_once ($interface);
                }
            }

            #include Traits file if exists
            foreach ($Tmiddleware_file_path as $traits) {
                if (file_exists($traits)) {
                    require_once ($traits);
                }
            }

            #include Abstracts file if exists
            foreach ($Amiddleware_file_path as $abstracts) {
                if (file_exists($abstracts)) {
                    require_once ($abstracts);
                }
            }

            #make first letter of directory  to uppercase
            $middleware_file_path = $this->array_walk($middleware_file_path);
            if (!file_exists($middleware_file_path)) {
                throw new MiddlewareNotFoundException($middleware_file_path);
            }

            require_once ($middleware_file_path);

            $clsName = explode("/", str_replace(".php", "", $middleware_file_path));
            $className = end($clsName);
            $clsObj = new $className;
            $method_name = 'auth';
            call_user_func_array(array($clsObj, $method_name), array());
        }
    }

    private function _reset_optional_params($params) {
        $rtnParams = array();
        foreach ($params as $key => $row) {
            $key = str_replace("?", "", $key);
            $rtnParams[$key] = urldecode($row);
        }
        return $rtnParams;
    }

    private function array_walk($string) {
        $string = explode("/", $string);
        array_walk($string, function (&$value) {
            $value = strtolower($value);
        });
        $return = implode("/", $string);
        return $return;
    }

    private function _get_filter_function_name() {
        $clsName = explode("/", str_replace(".php", "", $this->controller));
        $className = end($clsName);
        $class_methods_name_list = get_class_methods($className);

        if (!$class_methods_name_list) {
            throw new ControllerMethodNotFoundException($this->controller, []);
        }

        #now get matched methods in class from routing
        $matched_methods = array();
        foreach ($this->method as $mRow) {
            if (is_array($class_methods_name_list))
                foreach ($class_methods_name_list as $cRow) {
                    if ($mRow === $cRow) {
                        $matched_methods[] = $mRow;
                    }
                }
        }

        $this->method = $matched_methods;
        $params = array();
        foreach ($this->params as $key => $param) {
            if (!is_numeric($key)) {
                $key = str_replace("?", "", $key);
                $params[$key] = $param;
            }
        }

        $matched_params = array();
        $matched_function_no_params = array();
        #now matched parameters of each function and reduece the function if that function has parameters

        foreach ($this->method as $function_name) {
            $paramList = $this->_get_function_parameters($className, $function_name);
            #now match parameters if parameters exists
            if (count($paramList)) {
                if (count($params) === count($paramList)) {
                    #now check each field if matched and
                    $mtot = 0;
                    foreach ($params as $r => $v) {
                        foreach ($paramList as $s) {
                            if ($r === $s) {
                                $mtot++;
                            }
                        }
                    }
                    if ($mtot === count($params)) {
                        $matched_params[$function_name] = $paramList;
                    }
                } else {
                    #now filter the function name and its parameters if matched fully with optional parameters
                    $total_params = count($params);
                    $total_params_excluding_optional = 0;
                    foreach ($paramList as $key => $row) {
                        if (!is_numeric($key)) {
                            $total_params_excluding_optional++;
                        }
                    }
                    if ($total_params + $total_params_excluding_optional === count($paramList)) {
                        $mtot = 0;
                        foreach ($params as $r => $v) {
                            foreach ($paramList as $key => $s) {
                                if (is_numeric($key)) {
                                    if ($r === $s) {
                                        $mtot++;
                                    }
                                }
                            }
                        }
                        if ($mtot === count($params)) {
                            $matched_params[$function_name] = $paramList;
                        }
                    }
                }
            } else {
                $matched_function_no_params[] = $function_name;
            }
        }
        #now match for strict parameters otherwise fetch for optional parameters
        $strict_match = false;
        foreach ($matched_params as $key => $rows) {
            $tot = 0;
            foreach ($rows as $k => $v) {
                if (is_numeric($k)) {
                    $tot++;
                }
            }
            if ($tot === count($params)) {
                $strict_match = $key;
                break;
            }
        }

        if (!$strict_match) {
            foreach ($matched_params as $key => $rows) {
                $tot = 0;
                $otot = 0; #optional tot
                foreach ($rows as $k => $v) {
                    if (is_numeric($k)) {
                        $tot++;
                    } else {
                        $otot++;
                    }
                }
                if (($tot + $otot) === count($params)) {
                    $strict_match = $key;
                    break;
                }
            }
        }

        if (!$strict_match) {
            if (count($matched_function_no_params)) {
                $strict_match = current($matched_function_no_params);
            }
        }

        if ($strict_match) {
            return array(
                $strict_match => isset($matched_params[$strict_match]) ? $matched_params[$strict_match] : array()
            );
        } else {
            return false;
        }
    }

    private function _get_function_parameters($className, $methodName) {
        $reflection = new ReflectionMethod($className, $methodName);
        $paramList = array();
        foreach ($reflection->getParameters() as $key => $param) {
            if ($param->isDefaultValueAvailable()) {
                $paramList[$param->name] = $param->name;
            } else {
                $paramList[] = $param->name;
            }
        }
        return $paramList;
    }

    private function _get_main_routing_info() {
        $is_module = false;
        if (MODULE_STRUCTURE === true || MODULE_STRUCTURE === TRUE || MODULE_STRUCTURE > 0) {
            $is_module = true;
        }
    }

    private function _get_params_of_url($url_info) {

        $url_info = explode("/", $url_info);
        $main_url = explode("/", $this->uri);
        $params = array();

        for ($i = 0; $i < count($url_info); $i++) {
            $value = $url_info[$i];
            if ($this->_is_any_parameter($value)) {
                for ($j = $i; $j < count($main_url); $j++) {
                    $params[] = isset($main_url[$j]) ? $main_url[$j] : '';
                }
                break;
            }
            if ($this->_is_parametrised($value)) {
                $value = preg_replace('/[{}]/', '', $value);
                $params[$value] = isset($main_url[$i]) ? $main_url[$i] : '';
                continue;
            }
            if ($this->_is_optional($value)) {
                $value = preg_replace('/[{}]/', '', $value);
                $params[$value] = isset($main_url[$i]) ? $main_url[$i] : '';
                continue;
            }
        }
        return $params;
    }
    
    /**
     * Handle Nexo Framework specific exceptions
     * 
     * @param NexoException $e
     */
    private function handleNexoException(NexoException $e) {
        $context = $e->getContext();
        $errorData = [
            'error' => get_class($e),
            'message' => $e->getMessage(),
            'code' => $e->getCode(),
            'context' => $context,
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $e->getTraceAsString()
        ];
        
        // Log the error
        $this->logError($errorData);
        
        // Display appropriate error page based on environment
        $this->displayErrorPage($errorData);
    }
    
    /**
     * Handle generic PHP exceptions
     * 
     * @param Exception $e
     */
    private function handleGenericException(Exception $e) {
        $errorData = [
            'error' => 'UnexpectedException',
            'message' => $e->getMessage(),
            'code' => $e->getCode() ?: 500,
            'context' => [],
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $e->getTraceAsString()
        ];
        
        // Log the error
        $this->logError($errorData);
        
        // Display appropriate error page
        $this->displayErrorPage($errorData);
    }
    
    /**
     * Log error information
     * 
     * @param array $errorData
     */
    private function logError(array $errorData) {
        $logMessage = sprintf(
            "[%s] %s: %s in %s:%d\nContext: %s\nTrace:\n%s\n",
            date('Y-m-d H:i:s'),
            $errorData['error'],
            $errorData['message'],
            $errorData['file'],
            $errorData['line'],
            json_encode($errorData['context']),
            $errorData['trace']
        );
        
        // Log to file if logging is enabled
        if (defined('NEXO_LOG_ERRORS') && NEXO_LOG_ERRORS === true) {
            $logFile = defined('NEXO_LOG_FILE') ? NEXO_LOG_FILE : APPPATH . 'logs/nexo_errors.log';
            error_log($logMessage, 3, $logFile);
        }
        
        // Also log to PHP error log
        error_log($logMessage);
    }
    
    /**
     * Display error page based on environment and error type
     * 
     * @param array $errorData
     */
    private function displayErrorPage(array $errorData) {
        // Set appropriate HTTP status code
        $httpCode = $this->getHttpStatusCode($errorData['code']);
        http_response_code($httpCode);
        
        // Check if we're in development or production mode
        $isDevelopment = defined('NEXO_ENVIRONMENT') && NEXO_ENVIRONMENT === 'development';
        
        if ($isDevelopment) {
            // Show detailed error in development
            $this->displayDetailedError($errorData);
        } else {
            // Show generic error in production
            $this->displayGenericError($errorData);
        }
        
        exit;
    }
    
    /**
     * Get appropriate HTTP status code for error
     * 
     * @param int $errorCode
     * @return int
     */
    private function getHttpStatusCode($errorCode) {
        $statusCodes = [
            404 => 404, // Not Found
            500 => 500, // Internal Server Error
            403 => 403, // Forbidden
            400 => 400, // Bad Request
        ];
        
        return $statusCodes[$errorCode] ?? 500;
    }
    
    /**
     * Display detailed error for development environment
     * 
     * @param array $errorData
     */
    private function displayDetailedError(array $errorData) {
        echo "<!DOCTYPE html>\n";
        echo "<html><head><title>Nexo Framework Error</title>";
        echo "<style>body{font-family:Arial,sans-serif;margin:40px;background:#f5f5f5}";
        echo ".error-container{background:white;padding:30px;border-radius:8px;box-shadow:0 2px 10px rgba(0,0,0,0.1)}";
        echo ".error-title{color:#e74c3c;font-size:24px;margin-bottom:20px}";
        echo ".error-message{font-size:18px;margin-bottom:15px;color:#2c3e50}";
        echo ".error-details{background:#ecf0f1;padding:15px;border-radius:4px;margin:10px 0}";
        echo ".error-trace{background:#2c3e50;color:white;padding:15px;border-radius:4px;font-family:monospace;font-size:12px;overflow-x:auto}";
        echo "</style></head><body>";
        
        echo "<div class='error-container'>";
        echo "<h1 class='error-title'>🚨 Nexo Framework Error</h1>";
        echo "<div class='error-message'><strong>Error:</strong> " . htmlspecialchars($errorData['error']) . "</div>";
        echo "<div class='error-message'><strong>Message:</strong> " . htmlspecialchars($errorData['message']) . "</div>";
        
        if (!empty($errorData['context'])) {
            echo "<div class='error-details'><strong>Context:</strong><br>";
            echo "<pre>" . htmlspecialchars(json_encode($errorData['context'], JSON_PRETTY_PRINT)) . "</pre></div>";
        }
        
        echo "<div class='error-details'><strong>File:</strong> " . htmlspecialchars($errorData['file']) . "</div>";
        echo "<div class='error-details'><strong>Line:</strong> " . $errorData['line'] . "</div>";
        echo "<div class='error-trace'><strong>Stack Trace:</strong><br>" . htmlspecialchars($errorData['trace']) . "</div>";
        echo "</div></body></html>";
    }
    
    /**
     * Display generic error for production environment
     * 
     * @param array $errorData
     */
    private function displayGenericError(array $errorData) {
        $errorCode = $errorData['code'];
        $errorTitle = $this->getErrorTitle($errorCode);
        $errorMessage = $this->getErrorMessage($errorCode);
        
        echo "<!DOCTYPE html>\n";
        echo "<html><head><title>$errorTitle</title>";
        echo "<style>body{font-family:Arial,sans-serif;text-align:center;margin:100px}";
        echo ".error-code{font-size:72px;color:#e74c3c;margin-bottom:20px}";
        echo ".error-title{font-size:24px;color:#2c3e50;margin-bottom:10px}";
        echo ".error-message{font-size:16px;color:#7f8c8d}</style></head><body>";
        echo "<div class='error-code'>$errorCode</div>";
        echo "<div class='error-title'>$errorTitle</div>";
        echo "<div class='error-message'>$errorMessage</div>";
        echo "</body></html>";
    }
    
    /**
     * Get error title for HTTP status code
     * 
     * @param int $code
     * @return string
     */
    private function getErrorTitle($code) {
        $titles = [
            404 => 'Page Not Found',
            500 => 'Internal Server Error',
            403 => 'Access Forbidden',
            400 => 'Bad Request'
        ];
        
        return $titles[$code] ?? 'Application Error';
    }
    
    /**
     * Get error message for HTTP status code
     * 
     * @param int $code
     * @return string
     */
    private function getErrorMessage($code) {
        $messages = [
            404 => 'The page you are looking for could not be found.',
            500 => 'Something went wrong on our end. Please try again later.',
            403 => 'You do not have permission to access this resource.',
            400 => 'The request could not be understood by the server.'
        ];
        
        return $messages[$code] ?? 'An unexpected error occurred. Please try again later.';
    }
}

require_once(SYSTEMPATH . 'bind/loader.php');

#now create object of Nexo class

new Nexo;