<?php

/**
 * 
 */
$GLOBALS['libraries'] = isset($autoload['libraries']) ? $autoload['libraries'] : array();
$GLOBALS['helpers'] = isset($autoload['helpers']) ? $autoload['helpers'] : array();
$GLOBALS['models'] = isset($autoload['models']) ? $autoload['models'] : array();
$GLOBALS['languages'] = isset($autoload['languages']) ? $autoload['languages'] : array();

class gloader {

    protected $_g_functionname;
    protected $_g_functionparams;
    public $session;
    public $cookie;
    public $helper;
    public $library;
    public $model;
    private $defaulttimeout = 180;
    private $_protocalsplitter = "|||";
    private $_orderservertodevice = array('gts', 'dt', 'st', 'ra', 'raparam', 'data', 'sql', 'ct', 'ks', 'info', 'debug');
    private $_orderdevicetoserver = array('ukey', 'dt', 'raparam', 'data', 'ct', 'info', 'other');
    private $requestdata = array();
    private $responsetdata = array();

    //public $autoload = false;

    function __construct() {
        $this->helper = (object) array();
        $this->library = (object) array();
        $this->model = (object) array();
        /*
         *
         * AUTO LOAD helpers, libraries, models, languages, files and webcontent (using curl)
         *
         */

        $this->_init_autoload();
    }

    function __call($func, $params) {

        $this->_g_functionname = $func;
        $this->_g_functionparams = $params;

        if (!is_array($this->_g_functionparams) || count($this->_g_functionparams) > 3) {
            die("<hr>Warning: layout '" . $this->_g_functionname . "' parameters not exceed than 3");
        }

        if (count($this->_g_functionparams) < 1) {
            die("<hr>Warning: layout '" . $this->_g_functionname . "' needs first parameter");
        }

        $flag = false;
        $suffix_directory = 'layouts';
        #get directory where layouts can be found
        $library_directory_list = $this->get_directory_list($suffix_directory);
        #remove base layouts or last item
        array_pop($library_directory_list);
        #now search the available layout if present
        foreach ($library_directory_list as $layout_directory) {
            #get layout file path
            $layout_common_file_path = $this->array_walk($layout_directory . $this->_g_functionname);
            #layout files
            $layout_file = $layout_common_file_path . ".php";
            //echo '<hr>';
            #check if layout file exists
            if (file_exists($layout_file)) {
                $flag = true;
                break;
            }
        }

        #check if layout file not exists
        if ($flag == false) {
            die("<hr>Warning: Layout file '" . $layout_file . "' not found");
        }

        $data = array();
        $data = isset($this->_g_functionparams[1]) ? $this->_g_functionparams[1] : array();

        #for layout config array
        $gdata = array();
        $gdata = $data;

        #get view file name
        $view_file = $this->_g_functionparams[0] . ".php";

        #get view file contents
        $gdata['_body_'] = self::view($view_file, $data, TRUE);

        if (file_exists($common_files = $layout_common_file_path . '/_config.php')) {
            $common_files = File($common_files);
            foreach ($common_files as $file) {
                if (substr($file, 0, 1) === '#') {
                    continue;
                }
                $file = trim($file);
                $file = rtrim($file, ".php");
                $file_path = $layout_common_file_path . '/' . trim($file) . '.php';

                if (file_exists($file_path)) {
                    $gdata[$file] = self::view($file_path, $data, TRUE, TRUE);
                }
            }
        }

        $return = isset($this->_g_functionparams[2]) ? $this->_g_functionparams[2] : false;
        $is_layout = true;

        #get layout contents
        $layout_file = self::view($layout_file, $gdata, $return, $is_layout);

        #return contents if true
        if ($return) {
            return $layout_file;
        }
    }

    private function array_walk($string) {
        $string = explode("/", $string);
        array_walk($string, function (&$value) {
            $value = strtolower($value);
        });
        $return = implode("/", $string);
        return $return;
    }

    private function _init_autoload() {
        if (isset($GLOBALS['internal-path'])) {
            $internalpath = $GLOBALS['internal-path'];
            unset($GLOBALS['internal-path']);
        }
        $autoloads = array('helpers', 'libraries', 'models', 'languages', 'package');
        foreach ($autoloads as $loads) {
            if (isset($GLOBALS[$loads])) {
                foreach ($GLOBALS[$loads] as $file) {
                    $file = strtolower($file);
                    switch ($loads) {
                        case 'helpers' : $this->helper($file);
                            break;
                        case 'libraries' : $this->library($file);
                            break;
                        case 'models' : $this->model($file);
                            break;
                        case 'languages' : $this->language($file);
                            break;
                        case 'package' : $this->package($file);
                            break;
                    }
                }
            }
        }
        if (isset($internalpath)) {
            $GLOBALS['internal-path'] = $internalpath;
        }
    }

    public function appcontent($info) {
        $appurl = $GLOBALS['base_url'] . '/';

        #REQUEST MANIPULATION
        $debug = '';

        $mobiletoserverkeys = array('ukey' => '', 'dt' => '', 'raparam' => '', 'data' => '', 'ct' => '', 'info' => '', 'others' => '');
        $appversion = '0.19.5';
        $deviceid = '8787-5454-2154-8798-215-56564';
        $webinfo = array(
            'app-version' => $appversion,
            'os-version' => '11.3.1',
            'device-id' => $deviceid,
            'battery-status' => '12%',
            'app-memory-used' => '123.23 MB',
            'device-memory-remaining' => '658 MB',
            'internet-source' => 'Wi-fi',
            'lat' => '22.7157194934268',
            'long' => '75.8857282567561',
            'timezone-offset-value' => '19800000',
            'device-model' => 'ios_iPhone6',
            'current-location' => 'Manorama Ganj',
            'city' => 'Indore',
            'province/state' => 'Madhya Pradesh',
            'country' => 'India',
            'pin-code' => '452001',
            'dbversion' => '0'
        );

        $webinfo = implode('#', $webinfo);
        $ukey = isset($info['ukey']) ? $info['ukey'] : '00000';
        $dt = time() * 1000;
        $raparam = '';
        $data = isset($info['data']) ? $info['data'] : ''; #json or others
        $ct = isset($info['ct']) ? $info['ct'] : 'Y000';
        $others = '';
        $hexadecimal = true;
        $hex = '';
        if ($hexadecimal) {
            $hex = 't=a&';
        }

        $appurl = $appurl . '?' . $hex;

        #now set final values 
        $mobiletoserverkeys['ukey'] = $ukey;
        $mobiletoserverkeys['dt'] = $dt;
        $mobiletoserverkeys['raparam'] = $raparam;
        $mobiletoserverkeys['data'] = $data;
        $mobiletoserverkeys['ct'] = $ct;
        $mobiletoserverkeys['info'] = $webinfo;
        $mobiletoserverkeys['others'] = $others;
        $this->requestdata = $mobiletoserverkeys;

        $data = $this->createstring($mobiletoserverkeys);

        #now do encryption here
        include_once(SYSTEMPATH . '/bind/aescryption.php');
        $aes = new aescryption;
        $data = $aes->encrypt($data, false);

        $postdata = ['q' => $data, 'd' => $debug];

        $info = array(
            'url' => $appurl,
            'method' => 'POST',
            'data' => $postdata
        );

        $response = $this->webcontent($info);

        #RESPONSE MANIPULATION
        $status = $hexadecimal ? false : true;
        $res = $aes->decrypt(isset($response['data']['response']) ? $response['data']['response'] : '', $status);

        if ($status) {
            $res = @gzuncompress($res);
        }

        $res = $this->extractdata($res, 'mobile');
        $this->responsetdata = $res;
        $res = array(
            'executiontime' => isset($response['exetime']) ? $response['exetime'] : '',
            'serverurl' => isset($appurl) ? $appurl : '',
            'ipaddress' => $_SERVER['REMOTE_ADDR'],
            'request' => $this->requestdata,
            'response' => $this->responsetdata
        );
        return $res;
    }

    private function extractdata($encodedstring = "", $envioronment = 'server') {
        $info = (object) $this->createkeyvaluepair($encodedstring, $envioronment);
        return $info;
    }

    private function createkeyvaluepair($infostring, $envioronment) {
        $info = explode($this->_protocalsplitter, $infostring);
        $key = $envioronment == 'server' ? $this->_orderdevicetoserver : $this->_orderservertodevice;
        if (count($info) > count($key)) {
            unset($info[count($info) - 1]);
        }

        if (count($key) === count($info)) {
            $info = @array_combine($key, $info);
            return $info;
        } else {
            return 'error';
        }
    }

    private function createstring($arr) {
        $response = array();
        foreach ($this->_orderdevicetoserver as $key) {
            $response[$key] = isset($arr[$key]) ? $arr[$key] : 'xx-xx-error';
        }
        $response['other'] = '';
        $response = implode($this->_protocalsplitter, $response);
        return $response;
    }

    public function view($file, $data = array(), $returnView = false, $is_layout = false) {
        $route_view = $GLOBALS['route_view'];
        if (!$is_layout) {
            $fileArr = explode("/", $file);
            if (count($fileArr) > 1) {
                $route_view = explode("/", $route_view);
                unset($route_view[count($route_view) - 1]);
                $route_view = implode("/", $route_view);
            }
        }

        if (!is_array($data)) {
            $data = array();
        }
        extract($data);

        if (!$is_layout) {
            $file = $this->array_walk(str_replace("//", "/", rtrim($route_view, "/") . "/" . (preg_replace('/.php$/', '', $file)) . ".php"));
        }

        if (!file_exists($file)) {
            die("Warning: File '" . $file . "' not exists");
        }

        ob_start();

        eval('?>' . preg_replace('/;*\s*\?>/', '; ?>', str_replace('<?=', '<?php echo ', file_get_contents($file))));

        // Return the file data if requested
        if ($returnView === TRUE || $returnView === 1 || $returnView === true) {
            $buffer = ob_get_contents();
            @ob_end_clean();
            return $buffer;
        }
    }

    /**
     * 
     * @param type $info = [
      {
      "url": "http://localhost",
      "method": "post",
      "data": "Test Data",
      "return": true,
      "header": [],
      "auth": ""
      }
      ]
     * @return type array(status=>success, data=>[data], exetime=>0.034)
     */
    public function webcontent($info, $data = array(), $method = 'get', $headers = array(), $auths = '') {
        #check for old version 
        if (!is_array($info)) {
            return $this->_webcontent_old($info, $data, $method, $headers, $auths);
        }

        #check for single array
        if (isset($info['url'])) {
            $info = array($info);
        }

        #checking for valid url
        foreach ($info as $row) {
            $url = isset($row['url']) ? $row['url'] : false;

            #message for missing web url
            if (!$url) {
                $info = '<pre>' . print_r($info, true) . '</pre>';
                die("Warning: Missing web url in <br>" . $info);
            }

            #validate url
            if (!filter_var($url, FILTER_VALIDATE_URL)) {
                $info = '<pre>' . print_r($info, true) . '</pre>';
                die("Warning: Invalid web url <h4>{$url}</h4> in following <br>" . $info);
            }
        }

        #seperate without wait response
        $loggeraction = false;
        foreach ($info as $i => $row) {
            $return = isset($row['return']) ? $row['return'] : true;
            if ($return == true) {
                $loggeraction = true;
            }
        }

        foreach ($info as $i => $row) {
            $loggerurl = $this->baseurl('logs-manager');
            $url = isset($row['url']) ? $row['url'] : false;
            if (strstr($url, $loggerurl)) {
                $loggeraction = false;
            }
        }

        if (count($info)) {
            #create object
            require_once (SYSTEMPATH . 'bind/' . 'curlcontroller.php');
            $curlcontroller = new curlcontroller();
            #invoke curl for current request
            $res = $curlcontroller->webcontent($info);

            #now do logger action
            if ($loggeraction) {
                $info = array(
                    'url' => $this->baseurl('logs-manager'),
                    'method' => 'put',
                    'data' => json_encode($res),
                    'return' => false
                );
                #send data for logging purpose
                $curlcontroller->webcontent($info);
            }

            #now return curl response
            return $res;
        }
    }

    private function baseurl($file_path = '') {
        if (isset($GLOBALS['route_name'][$file_path])) {
            $file_path = $GLOBALS['route_name'][trim($file_path)];
        }

        $base_url = rtrim((isset($GLOBALS['base_url']) ? $GLOBALS['base_url'] : ''), "/") . "/" . ltrim($file_path, "/");
        return $base_url;
    }

    private function _webcontent_old($url, $data = array(), $method = 'get', $headers = array(), $auths = '') {
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            die("Warning: Wrong web url <h4>{$url}</h4>");
        }

        $method = strtoupper($method);

        if (in_array($method, array('GET', 'PUT'))) {
            $url = $url . '?' . http_build_query($data);
        } else if ($method == 'POST') {
            $url = $url;
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_ENCODING, '');
        curl_setopt($ch, CURLOPT_MAXREDIRS, '');
        curl_setopt($ch, CURLOPT_TIMEOUT, 0);
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
        if ($method == 'POST') {
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        }

        if (is_array($headers) && count($headers)) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }

        if (is_string($auths) && strlen(trim($auths))) {
            curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
            curl_setopt($ch, CURLOPT_USERPWD, $auths);
        }

        $response = curl_exec($ch);
        curl_close($ch);
        return $response;
    }

    public function file($filepath, $returnView = false) {
        if (!file_exists($filepath)) {
            die("Warning: File '" . $filepath . "' not exists");
        }
        $file = file_get_contents($filepath);
        if ($returnView) {
            return $file;
        } else {
            echo $file;
        }
    }

    public function package($package_name, $name = FALSE) {
        $package_dir = trim(APPPATH, '/') . '/' . 'packages/' . $package_name;
        $package_json = $package_dir . '/package.json';
        if (!file_exists($package_json)) {
            echo "Warning: File missing '" . $package_json . "'";
            exit;
        }

        $package_setting = json_decode(file_get_contents($package_json));

        if (!isset($package_setting->require)) {
            die('Package ' . $package_name . ' not found');
        }

        foreach ($package_setting->require as $include) {
            $file_name = $include->filename;
            #now include file if exists
            $file_path = $package_dir . "/" . $file_name;

            if (!file_exists($file_path)) {
                echo "Warning: File  '" . $file_name . "' missing in '" . $package_dir . "'";
                exit;
            }
            require_once($file_path);
        }
    }

    public function library($library_file, $name = FALSE, $params = array()) {
        $suffix_directory = 'Libraries';
        $this->set_object($library_file, $suffix_directory, $name, $params);
    }

    public function helper($helper_file, $name = FALSE, $params = array()) {
        $suffix_directory = 'Helpers';
        $this->set_object($helper_file, $suffix_directory, $name, $params);
    }

    public function model($model_file, $name = FALSE, $params = array()) {
        $suffix_directory = 'Models';
        $this->set_object($model_file, $suffix_directory, $name, $params);
    }

    public function languages($model_file, $name = FALSE) {
        //$suffix_directory = 'Languages';
        //$this->set_object($model_file, $suffix_directory, $name);
    }

    private function get_directory_list($suffix_directory = '') {
        $suffix_directory = strtolower($suffix_directory);
        $library_directory_list = array();
        $library_directory_list[$GLOBALS['current_directory']] = rtrim($GLOBALS['current_directory'], "/") . '/' . $suffix_directory . '/';
        $libArray = explode("controllers", $GLOBALS['current_directory']);
        #receive library directory path
        $cnt = count($libArray);
        $n = 0;
        foreach ($libArray as $library_directory) {
            $n++;
            if ($cnt != $n) {
                $library_directory_list[$library_directory] = rtrim($library_directory, "/") . '/' . $suffix_directory . '/';
            }
        }

        $libArray = $library_directory_list;
        $library_directory_list = array();
        #reset library array
        foreach ($libArray as $library_directory) {
            $library_directory_list[] = $library_directory;
        }

        if (isset($GLOBALS['internal-path'])) {
            #now add app directory
            $library_directory_list[] = $GLOBALS['internal-path'] . '/' . $suffix_directory . '/';
        } else {
            #now add app directory
            $library_directory_list[] = MAIN_DIRECTORY . '/' . APP_DIRECTORY . '/' . $suffix_directory . '/';
        }
        #now add root directory
        $library_directory_list[] = SYSTEMPATH . $suffix_directory . '/';
        $library_directory_list = array_unique($library_directory_list);

        if ($suffix_directory == 'libraries') {
            if (!in_array('webapps/apps/libraries/', $library_directory_list)) {
                $library_directory_list[] = 'webapps/apps/libraries/';
            }
        }

        if ($suffix_directory == 'helpers') {
            if (!in_array('webapps/apps/helpers/', $library_directory_list)) {
                $library_directory_list[] = 'webapps/apps/helpers/';
            }
        }

        if ($suffix_directory == 'models') {
            if (!in_array('webapps/apps/models/', $library_directory_list)) {
                $library_directory_list[] = 'webapps/apps/models/';
            }
        }
        #return directory list
        return $library_directory_list;
    }

    private function _directory_list($dir_path) {
        $file_list = array();
        $route_directory = $dir_path;
        if (!is_dir($route_directory)) {
            echo "<hr>Warning: routes directory '" . $route_directory . "' not found";
            exit;
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

    private function set_object($directory_file, $suffix_directory, $name, $params = array()) {
        $is_exist = false;
        $directory_file = str_replace(".php", "", $directory_file);

        $_file = explode("/", $directory_file);
        $_file = end($_file);

        $class_name = $_file;
        $library_directory_list = $this->get_directory_list($suffix_directory);

        foreach ($library_directory_list as $library_directory) {
            $base_flag = true;

            if (!isset($GLOBALS['internal-path']) && strstr($library_directory, SYSTEM_DIRECTORY)) {
                $file = $this->array_walk($library_directory . $directory_file) . '/g' . $this->array_walk($_file) . '.php';
                //interface class
                $Ifile[] = $this->array_walk($library_directory . $directory_file) . '/ig' . $this->array_walk($_file) . '.php';
                //abstract class
                $Afile[] = $this->array_walk($library_directory . $directory_file) . '/ag' . $this->array_walk($_file) . '.php';
                //traits class
                $Tfile[] = $this->array_walk($library_directory . $directory_file) . '/tg' . $this->array_walk($_file) . '.php';
            } else {
                $file = $this->array_walk($library_directory . $directory_file) . '/' . $this->array_walk($_file) . '.php';
                //interface class
                $Ifile[] = $this->array_walk($library_directory . $directory_file) . '/i' . $this->array_walk($_file) . '.php';
                //abstract class
                $Afile[] = $this->array_walk($library_directory . $directory_file) . '/a' . $this->array_walk($_file) . '.php';
                //traits class
                $Tfile[] = $this->array_walk($library_directory . $directory_file) . '/t' . $this->array_walk($_file) . '.php';
            }

            if (file_exists($file)) {

                $is_exist = true;

                $directory_path = $this->array_walk($library_directory . $directory_file);

                $Icontroller_file_path_dir = $directory_path . "/interfaces";
                if (is_dir($Icontroller_file_path_dir)) {
                    $list = $this->_directory_list($Icontroller_file_path_dir);
                    $Ifile = array_merge($Ifile, $list);
                }

                $Tcontroller_file_path_dir = $directory_path . "/traits";
                if (is_dir($Tcontroller_file_path_dir)) {
                    $list = $this->_directory_list($Tcontroller_file_path_dir);
                    $Tfile = array_merge($Tfile, $list);
                }

                $Acontroller_file_path_dir = $directory_path . "/abstracts";
                if (is_dir($Acontroller_file_path_dir)) {
                    $list = $this->_directory_list($Acontroller_file_path_dir);
                    $Afile = array_merge($Afile, $list);
                }

                #include interfaces file if exists
                foreach ($Ifile as $interface) {
                    if (file_exists($interface)) {
                        require_once ($interface);
                    }
                }
                #include Traits file if exists
                foreach ($Tfile as $traits) {
                    if (file_exists($traits)) {
                        require_once ($traits);
                    }
                }
                #include Abstract class
                foreach ($Afile as $abstracts) {
                    if (file_exists($abstracts)) {
                        require_once ($abstracts);
                    }
                }



                #include controllers
                require_once($file);

                #Get namespace if exists
                $ns = $this->get_namespace($file);

                if ($ns) {
                    if ($name == FALSE) {
                        $name = $class_name;
                    }
                    $class_name = "{$ns}\\{$class_name}";
                }

                if (!class_exists($class_name)) {
                    echo "<hr>Warning: '" . $suffix_directory . "' class '" . $class_name . "' does not exists in file '" . $file . "'";
                    exit(3);
                }

                #now create object
                $obj = $class_name;
                $name = trim($name);

                if (!empty($name)) {
                    $obj = $name;
                }

                $obj = strtolower(trim($obj));
                if ($base_flag) {
                    switch ($suffix_directory) {
                        case 'Helpers':
                            $this->helper->{$obj} = NULL;
                            $this->helper->{$obj} = new $class_name($params);
                            break;
                        case 'Libraries':
                            $this->library->{$obj} = NULL;
                            $this->library->{$obj} = $ob = new $class_name($params);
                            if ($obj === 'session' || $obj === 'cookie') {
                                $this->{$obj} = $ob;
                            }
                            break;
                        case 'Models':
                            $this->model->{$obj} = NULL;
                            $this->model->{$obj} = new $class_name($params);
                            break;
                        case 'Languages':
                            $this->language->{$obj} = NULL;
                            $this->language->{$obj} = new $class_name;
                            break;
                    }
                } else {
                    $this->library->{$obj} = NULL;
                    $this->library->{$obj} = new $class_name;
                }
                break;
            }
        }

        if (!isset($GLOBALS['internal-path']) && !$is_exist) {
            echo "<hr>Warning: " . $suffix_directory . " '" . $file . "' not found";
            exit(3);
        }

        if (!$is_exist) {
            echo "<hr>Warning: " . $suffix_directory . " '" . $file . "' not found";
            exit(3);
        }
    }

    private function get_namespace($file) {
        $ns = FALSE;
        $handle = fopen($file, "r");
        if ($handle) {
            while (($line = fgets($handle)) !== false) {
                if (strpos($line, 'namespace') === 0) {
                    $parts = explode(' ', $line);
                    $ns = rtrim(trim($parts[1]), ';');
                    break;
                }
            }
            fclose($handle);
        }
        return $ns;
    }

    private function get_classname($file) {
        $cl = FALSE;
        $handle = fopen($file, "r");
        if ($handle) {
            while (($line = fgets($handle)) !== false) {
                $bline = strtolower($line);
                if (strpos($bline, 'class') === 0) {
                    $parts = explode(' ', $line);
                    $cl = rtrim(trim($parts[1]), ';');
                    break;
                }
            }
            fclose($handle);
        }
        return $cl;
    }
}
