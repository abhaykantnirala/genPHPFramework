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

// 🔒 Secure session initialization handled in index.php

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

require_once(SYSTEMPATH . 'bind/nexo-security.php');

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
    
    // Performance optimization properties
    private static $routeCache = [];
    private static $classMethodCache = [];
    private static $reflectionCache = [];
    private $performanceMetrics = [];
    private $cacheEnabled = true;
    
    // New innovative features
    private static $dependencyContainer = [];
    private static $eventListeners = [];
    private $middlewareStack = [];
    private $requestPipeline = [];
    private static $autoDiscoveredRoutes = null;

    function __construct() {
        // Initialize performance tracking
        $this->stime = microtime(true);
        $this->performanceMetrics['start_time'] = $this->stime;
        
        #get uri object
        $this->obj = array();
        $this->params = array();
        
        // Enable caching based on environment
        $this->cacheEnabled = !defined('NEXO_ENVIRONMENT') || NEXO_ENVIRONMENT !== 'development';
        
        try {
            $this->run();
        } catch (NexoException $e) {
            $this->handleNexoException($e);
        } catch (Exception $e) {
            $this->handleGenericException($e);
        }
        
        // Log performance metrics in development
        $this->logPerformanceMetrics();
    }

    /**
     * Main execution method for the Nexo Framework
     * Handles the complete request lifecycle from routing to response
     * 
     * @throws ControllerNotFoundException If controller file not found
     * @throws RouteNotFoundException If no matching route found
     * @throws MiddlewareNotFoundException If middleware file not found
     * @return void
     */
    public function run(): void {
        // Fire framework initialization event
        self::fire('nexo.framework.init', $this);
        
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

        // Fire before controller execution event
        self::fire('nexo.controller.before', [
            'controller' => $this->controller,
            'method' => $this->method,
            'params' => $this->params
        ]);

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
            
            // Fire before method execution event
            self::fire('nexo.method.before', [
                'class' => $className,
                'method' => $method_name,
                'params' => $fun_params
            ]);
            
            call_user_func_array(array($clsObj, $method_name), $fun_params);
        } else {
            // Fire before method execution event
            self::fire('nexo.method.before', [
                'class' => $className,
                'method' => $method_name,
                'params' => []
            ]);
            
            call_user_func_array(array($clsObj, $method_name), array());
        }
        
        // Fire after controller execution event
        self::fire('nexo.controller.after', [
            'controller' => $this->controller,
            'method' => $method_name,
            'class' => $className
        ]);
    }

    /**
     * Get list of PHP files from a directory
     * 
     * @param string $dir_path Directory path to scan
     * @throws DirectoryNotFoundException If directory doesn't exist
     * @return array List of PHP file paths
     */
    private function _directory_list(string $dir_path): array {
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

    /**
     * Reset and sanitize optional parameters with security validation
     * 
     * @param array $params Raw parameters from routing
     * @return array Sanitized and validated parameters
     */
    private function _reset_optional_params($params) {
        $rtnParams = array();
        foreach ($params as $key => $row) {
            // Sanitize parameter key
            $key = str_replace("?", "", $key);
            $key = NexoSecurity::sanitize($key, 'string');
            
            // Validate and sanitize parameter value
            $decodedValue = urldecode($row);
            list($sanitizedValue, $isValid, $errors) = NexoSecurity::validateInput($decodedValue, [
                'type' => 'string',
                'length' => ['max' => 1000], // Prevent extremely long inputs
            ]);
            
            if (!$isValid) {
                // Log security violation but continue with sanitized value
                foreach ($errors as $error) {
                    NexoSecurity::logSecurityViolation(
                        'PARAMETER_VALIDATION_FAILED',
                        "Key: $key, Value: $decodedValue, Error: $error"
                    );
                }
            }
            
            $rtnParams[$key] = $sanitizedValue;
        }
        return $rtnParams;
    }

    /**
     * Convert string path to lowercase format
     * 
     * @param string $string Path string to convert
     * @return string Converted lowercase path
     */
    private function array_walk(string $string): string {
        $string = explode("/", $string);
        array_walk($string, function (&$value) {
            $value = strtolower($value);
        });
        $return = implode("/", $string);
        return $return;
    }

    /**
     * Get the best matching function for the current route
     * 
     * @return array|false Array with method name and parameters, or false if no match
     */
    private function _get_filter_function_name() {
        $className = $this->_getControllerClassName();
        $class_methods = get_class_methods($className);

        if (!$class_methods) {
            throw new ControllerMethodNotFoundException($this->controller, []);
        }

        $matched_methods = $this->_getMatchedMethods($class_methods);
        $this->method = $matched_methods;
        
        $params = $this->_normalizeParams();
        $matched_params = [];
        $matched_function_no_params = [];

        // Match functions with their parameters
        foreach ($matched_methods as $function_name) {
            $paramList = $this->_get_function_parameters($className, $function_name);
            
            if (count($paramList)) {
                if ($this->_isExactParameterMatch($params, $paramList)) {
                    $matched_params[$function_name] = $paramList;
                } elseif ($this->_isOptionalParameterMatch($params, $paramList)) {
                    $matched_params[$function_name] = $paramList;
                }
            } else {
                $matched_function_no_params[] = $function_name;
            }
        }

        // Find the best match
        return $this->_findBestMatch($matched_params, $matched_function_no_params, $params);
    }
    
    /**
     * Get the controller class name from the controller file path
     * 
     * @return string The controller class name
     */
    private function _getControllerClassName(): string {
        $clsName = explode("/", str_replace(".php", "", $this->controller));
        return end($clsName);
    }
    
    /**
     * Get methods that match the routing methods (with caching)
     * 
     * @param array $class_methods List of class methods
     * @return array Matched methods
     */
    private function _getMatchedMethods(array $class_methods): array {
        $cache_key = md5(serialize($this->method) . serialize($class_methods));
        
        if ($this->cacheEnabled && isset(self::$classMethodCache[$cache_key])) {
            return self::$classMethodCache[$cache_key];
        }
        
        $matched_methods = [];
        foreach ($this->method as $route_method) {
            if (in_array($route_method, $class_methods)) {
                $matched_methods[] = $route_method;
            }
        }
        
        if ($this->cacheEnabled) {
            self::$classMethodCache[$cache_key] = $matched_methods;
        }
        
        return $matched_methods;
    }
    
    /**
     * Normalize parameters by removing optional indicators
     * 
     * @return array Normalized parameters
     */
    private function _normalizeParams(): array {
        $params = [];
        foreach ($this->params as $key => $param) {
            if (!is_numeric($key)) {
                $key = str_replace("?", "", $key);
                $params[$key] = $param;
            }
        }
        return $params;
    }
    
    /**
     * Check if parameters match exactly with function parameters
     * 
     * @param array $params Route parameters
     * @param array $paramList Function parameters
     * @return bool True if exact match
     */
    private function _isExactParameterMatch(array $params, array $paramList): bool {
        if (count($params) !== count($paramList)) {
            return false;
        }
        
        $match_count = 0;
        foreach ($params as $param_name => $value) {
            if (in_array($param_name, $paramList)) {
                $match_count++;
            }
        }
        
        return $match_count === count($params);
    }
    
    /**
     * Check if parameters match with optional parameters included
     * 
     * @param array $params Route parameters
     * @param array $paramList Function parameters
     * @return bool True if optional match
     */
    private function _isOptionalParameterMatch(array $params, array $paramList): bool {
        $required_params = 0;
        $optional_params = 0;
        
        foreach ($paramList as $key => $param) {
            if (is_numeric($key)) {
                $required_params++;
            } else {
                $optional_params++;
            }
        }
        
        if (count($params) + $optional_params !== count($paramList)) {
            return false;
        }
        
        $match_count = 0;
        foreach ($params as $param_name => $value) {
            foreach ($paramList as $key => $param) {
                if (is_numeric($key) && $param_name === $param) {
                    $match_count++;
                }
            }
        }
        
        return $match_count === count($params);
    }
    
    /**
     * Find the best matching function based on parameter matching
     * 
     * @param array $matched_params Functions with matched parameters
     * @param array $matched_function_no_params Functions without parameters
     * @param array $params Current route parameters
     * @return array|false Best match or false if none found
     */
    private function _findBestMatch(array $matched_params, array $matched_function_no_params, array $params) {
        // First, try strict parameter matching
        $strict_match = $this->_findStrictMatch($matched_params, $params);
        
        if (!$strict_match) {
            // Then try optional parameter matching
            $strict_match = $this->_findOptionalMatch($matched_params, $params);
        }
        
        if (!$strict_match && count($matched_function_no_params)) {
            // Finally, use functions with no parameters
            $strict_match = current($matched_function_no_params);
        }
        
        return $strict_match ? [
            $strict_match => $matched_params[$strict_match] ?? []
        ] : false;
    }
    
    /**
     * Find strict parameter match
     * 
     * @param array $matched_params Functions with matched parameters
     * @param array $params Current route parameters
     * @return string|false Method name or false
     */
    private function _findStrictMatch(array $matched_params, array $params) {
        foreach ($matched_params as $method_name => $param_list) {
            $required_count = 0;
            foreach ($param_list as $k => $v) {
                if (is_numeric($k)) {
                    $required_count++;
                }
            }
            if ($required_count === count($params)) {
                return $method_name;
            }
        }
        return false;
    }
    
    /**
     * Find optional parameter match
     * 
     * @param array $matched_params Functions with matched parameters
     * @param array $params Current route parameters
     * @return string|false Method name or false
     */
    private function _findOptionalMatch(array $matched_params, array $params) {
        foreach ($matched_params as $method_name => $param_list) {
            $required_count = 0;
            $optional_count = 0;
            
            foreach ($param_list as $k => $v) {
                if (is_numeric($k)) {
                    $required_count++;
                } else {
                    $optional_count++;
                }
            }
            
            if (($required_count + $optional_count) === count($params)) {
                return $method_name;
            }
        }
        return false;
    }

    /**
     * Get function parameters with reflection caching
     * 
     * @param string $className The class name
     * @param string $methodName The method name
     * @return array Array of parameters
     */
    private function _get_function_parameters($className, $methodName) {
        $cache_key = $className . '::' . $methodName;
        
        if ($this->cacheEnabled && isset(self::$reflectionCache[$cache_key])) {
            return self::$reflectionCache[$cache_key];
        }
        
        $reflection = new ReflectionMethod($className, $methodName);
        $paramList = array();
        foreach ($reflection->getParameters() as $key => $param) {
            if ($param->isDefaultValueAvailable()) {
                $paramList[$param->name] = $param->name;
            } else {
                $paramList[] = $param->name;
            }
        }
        
        if ($this->cacheEnabled) {
            self::$reflectionCache[$cache_key] = $paramList;
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
     * Check if parameter accepts any number of values (*parameter)
     * 
     * @param string $parameter The parameter to check
     * @return bool True if it's an any parameter
     */
    private function _is_any_parameter(string $parameter): bool {
        return strpos($parameter, '*') !== false;
    }
    
    /**
     * Check if parameter is parametrised ({parameter})
     * 
     * @param string $parameter The parameter to check
     * @return bool True if it's a parametrised parameter
     */
    private function _is_parametrised(string $parameter): bool {
        return preg_match('/^{[^?].*}$/', $parameter) === 1;
    }
    
    /**
     * Check if parameter is optional ({?parameter})
     * 
     * @param string $parameter The parameter to check
     * @return bool True if it's an optional parameter
     */
    private function _is_optional(string $parameter): bool {
        return preg_match('/^{\?.*}$/', $parameter) === 1;
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
    
    /**
     * Log performance metrics for development and optimization
     */
    private function logPerformanceMetrics(): void {
        $endTime = microtime(true);
        $executionTime = ($endTime - $this->stime) * 1000; // Convert to milliseconds
        
        $this->performanceMetrics['end_time'] = $endTime;
        $this->performanceMetrics['execution_time_ms'] = round($executionTime, 2);
        $this->performanceMetrics['memory_usage'] = memory_get_usage(true);
        $this->performanceMetrics['memory_peak'] = memory_get_peak_usage(true);
        
        // Only log in development environment
        if (defined('NEXO_ENVIRONMENT') && NEXO_ENVIRONMENT === 'development') {
            $this->performanceMetrics['cache_hits'] = [
                'route_cache' => count(self::$routeCache),
                'method_cache' => count(self::$classMethodCache),
                'reflection_cache' => count(self::$reflectionCache)
            ];
            
            // Log to performance log if enabled
            if (defined('NEXO_LOG_ERRORS') && NEXO_LOG_ERRORS === true) {
                $perfLog = sprintf(
                    "[%s] PERFORMANCE: %sms | Memory: %s | Peak: %s | Caches: R:%d M:%d Ref:%d\n",
                    date('Y-m-d H:i:s'),
                    $this->performanceMetrics['execution_time_ms'],
                    $this->formatBytes($this->performanceMetrics['memory_usage']),
                    $this->formatBytes($this->performanceMetrics['memory_peak']),
                    count(self::$routeCache),
                    count(self::$classMethodCache),
                    count(self::$reflectionCache)
                );
                
                $logFile = defined('NEXO_LOG_FILE') ? NEXO_LOG_FILE : APPPATH . 'logs/nexo_errors.log';
                error_log($perfLog, 3, $logFile);
            }
        }
    }
    
    /**
     * Format bytes into human readable format
     * 
     * @param int $bytes Number of bytes
     * @return string Formatted string
     */
    private function formatBytes(int $bytes): string {
        $units = ['B', 'KB', 'MB', 'GB'];
        $factor = floor((strlen($bytes) - 1) / 3);
        return sprintf("%.2f %s", $bytes / pow(1024, $factor), $units[$factor]);
    }
    
    /**
     * Get current performance metrics
     * 
     * @return array Performance metrics
     */
    public function getPerformanceMetrics(): array {
        return $this->performanceMetrics;
    }
    
    /**
     * Clear all caches (useful for development)
     */
    public static function clearCaches(): void {
        self::$routeCache = [];
        self::$classMethodCache = [];
        self::$reflectionCache = [];
    }
    
    /**
     * Get cache statistics
     * 
     * @return array Cache statistics
     */
    public static function getCacheStats(): array {
        return [
            'route_cache_size' => count(self::$routeCache),
            'method_cache_size' => count(self::$classMethodCache),
            'reflection_cache_size' => count(self::$reflectionCache),
            'total_cached_items' => count(self::$routeCache) + count(self::$classMethodCache) + count(self::$reflectionCache)
        ];
    }
    
    // ================================
    // INNOVATIVE NEW FEATURES
    // ================================
    
    /**
     * Dependency Injection Container
     * Register a service in the container
     * 
     * @param string $name Service name
     * @param callable|object $factory Service factory or instance
     * @param bool $singleton Whether to create singleton
     */
    public static function register(string $name, $factory, bool $singleton = true): void {
        self::$dependencyContainer[$name] = [
            'factory' => $factory,
            'singleton' => $singleton,
            'instance' => null
        ];
    }
    
    /**
     * Resolve a service from the container
     * 
     * @param string $name Service name
     * @return mixed Service instance
     * @throws Exception If service not found
     */
    public static function resolve(string $name) {
        if (!isset(self::$dependencyContainer[$name])) {
            throw new Exception("Service '$name' not found in dependency container");
        }
        
        $service = self::$dependencyContainer[$name];
        
        if ($service['singleton'] && $service['instance'] !== null) {
            return $service['instance'];
        }
        
        $instance = is_callable($service['factory']) 
            ? call_user_func($service['factory']) 
            : $service['factory'];
            
        if ($service['singleton']) {
            self::$dependencyContainer[$name]['instance'] = $instance;
        }
        
        return $instance;
    }
    
    /**
     * Event System - Register event listener
     * 
     * @param string $event Event name
     * @param callable $listener Event listener
     * @param int $priority Priority (lower = higher priority)
     */
    public static function listen(string $event, callable $listener, int $priority = 10): void {
        if (!isset(self::$eventListeners[$event])) {
            self::$eventListeners[$event] = [];
        }
        
        self::$eventListeners[$event][] = [
            'listener' => $listener,
            'priority' => $priority
        ];
        
        // Sort by priority
        usort(self::$eventListeners[$event], function($a, $b) {
            return $a['priority'] <=> $b['priority'];
        });
    }
    
    /**
     * Event System - Fire event
     * 
     * @param string $event Event name
     * @param mixed $data Event data
     * @return array Results from all listeners
     */
    public static function fire(string $event, $data = null): array {
        if (!isset(self::$eventListeners[$event])) {
            return [];
        }
        
        $results = [];
        foreach (self::$eventListeners[$event] as $listener) {
            $results[] = call_user_func($listener['listener'], $data);
        }
        
        return $results;
    }
    
    /**
     * Route Model Binding - Auto-inject model based on route parameter
     * 
     * @param string $paramName Parameter name
     * @param string $modelClass Model class name
     * @param string $keyField Field to search by (default: 'id')
     */
    public function bindModel(string $paramName, string $modelClass, string $keyField = 'id'): void {
        if (!isset($this->params[$paramName])) {
            return;
        }
        
        $value = $this->params[$paramName];
        
        // Try to resolve model instance
        try {
            if (class_exists($modelClass)) {
                $model = new $modelClass();
                
                // If model has find method, use it
                if (method_exists($model, 'find')) {
                    $instance = $model->find($value);
                } elseif (method_exists($model, 'where')) {
                    $instance = $model->where($keyField, $value)->first();
                } else {
                    // Fallback to manual property setting
                    $instance = $model;
                    $instance->$keyField = $value;
                }
                
                // Replace parameter with model instance
                $this->params[$paramName] = $instance;
            }
        } catch (Exception $e) {
            // Log error but don't break execution
            if (defined('NEXO_LOG_ERRORS') && NEXO_LOG_ERRORS) {
                error_log("Model binding failed for $paramName: " . $e->getMessage());
            }
        }
    }
    
    /**
     * Advanced Request Pipeline - Add request processor
     * 
     * @param callable $processor Request processor
     * @param int $priority Priority (lower = earlier execution)
     */
    public function addRequestProcessor(callable $processor, int $priority = 10): void {
        $this->requestPipeline[] = [
            'processor' => $processor,
            'priority' => $priority
        ];
        
        // Sort by priority
        usort($this->requestPipeline, function($a, $b) {
            return $a['priority'] <=> $b['priority'];
        });
    }
    
    /**
     * Process request through pipeline
     * 
     * @param array $request Request data
     * @return array Processed request
     */
    private function processRequestPipeline(array $request): array {
        foreach ($this->requestPipeline as $pipeline) {
            $request = call_user_func($pipeline['processor'], $request);
        }
        return $request;
    }
    
    /**
     * Auto-discover routes from controller annotations
     * 
     * @param string $controllerPath Path to controllers directory
     * @return array Discovered routes
     */
    public static function discoverRoutes(string $controllerPath): array {
        if (self::$autoDiscoveredRoutes !== null) {
            return self::$autoDiscoveredRoutes;
        }
        
        $routes = [];
        $files = glob($controllerPath . '/*.php');
        
        foreach ($files as $file) {
            $content = file_get_contents($file);
            $className = basename($file, '.php');
            
            // Look for route annotations like @Route("/path", methods=["GET"])
            preg_match_all('/@Route\s*\(\s*["\']([^"\']+)["\']\s*(?:,\s*methods\s*=\s*\[(.*?)\])?\s*\)\s*(?:.*?)\s*(?:public\s+)?function\s+(\w+)/s', $content, $matches, PREG_SET_ORDER);
            
            foreach ($matches as $match) {
                $path = $match[1];
                $methods = isset($match[2]) ? 
                    array_map('trim', explode(',', str_replace(['"', "'"], '', $match[2]))) : 
                    ['GET'];
                $method = $match[3];
                
                $routes[] = [
                    'path' => $path,
                    'methods' => $methods,
                    'controller' => $className,
                    'method' => $method
                ];
            }
        }
        
        self::$autoDiscoveredRoutes = $routes;
        return $routes;
    }
    
    /**
     * Debug information collector
     * 
     * @return array Comprehensive debug information
     */
    public function getDebugInfo(): array {
        return [
            'framework' => [
                'name' => 'Nexo Framework',
                'version' => '1.0.0',
                'environment' => defined('NEXO_ENVIRONMENT') ? NEXO_ENVIRONMENT : 'production'
            ],
            'request' => [
                'uri' => $this->uri,
                'controller' => $this->controller,
                'method' => $this->method,
                'params' => $this->params,
                'middleware' => $this->middleware
            ],
            'performance' => $this->performanceMetrics,
            'cache' => self::getCacheStats(),
            'dependencies' => array_keys(self::$dependencyContainer),
            'events' => array_keys(self::$eventListeners),
            'memory' => [
                'current' => memory_get_usage(true),
                'peak' => memory_get_peak_usage(true),
                'limit' => ini_get('memory_limit')
            ],
            'system' => [
                'php_version' => PHP_VERSION,
                'server' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown',
                'loaded_extensions' => get_loaded_extensions()
            ]
        ];
    }
}

require_once(SYSTEMPATH . 'bind/loader.php');

#now create object of Nexo class

new Nexo;