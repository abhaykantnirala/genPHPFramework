<?php

class route {

    private static $_instance = null;

    public static function middleware($_middlware = array()) {
        $arr = array();

        if (!is_array($_middlware)) {
            if (empty(trim($_middlware))) {
                $arr = array();
            } else {
                $arr [] = $_middlware;
            }
        } else {
            $arr[] = $_middlware;
        }
        if ($GLOBALS['is_group'] == true) {
            #self::group_middleware($arr);
        }

        if (self::$_instance === null) {
            self::$_instance = new self;
        }
        return self::$_instance;
    }

    private static function single_middleware($arr) {

        if (is_array($arr)) {
            $info = end($GLOBALS['route_info']);

            if (isset($info['middleware'])) {
                $middlewares = $info['middleware'];
                foreach ($middlewares[0] as $middleware) {
                    foreach ($arr as $k => $value) {
                        if (strtolower(trim($middleware)) === strtolower(trim($value))) {
                            unset($arr[$k]);
                        }
                    }
                }
                if (is_array($arr) && count($arr)) {
                    $info['middleware'][0] = array_merge($info['middleware'][0], $arr);
                }
            }

            $GLOBALS['route_info'][count($GLOBALS['route_info']) - 1] = $info;
        }
    }

    private static function group_middleware($arr) {
        if (is_array($arr)) {
            $info = $GLOBALS['route_info'];
            $info = array_reverse($info);
            $flag = false;
            foreach ($info as $key => $rows) {
                if (isset($rows['middleware_start'])) {
                    $flag = true;
                    unset($info[$key]);
                }
                if (isset($rows['middleware_end'])) {
                    $flag = false;
                    unset($info[$key]);
                }
                if ($flag) {
                    if (isset($info[$key]['middleware'])) {
                        $middlewares = $info[$key]['middleware'];
                        foreach ($middlewares[0] as $middleware) {
                            foreach ($arr as $k => $value) {
                                if (strtolower(trim($middleware)) === strtolower(trim($value))) {
                                    unset($arr[$k]);
                                }
                            }
                        }
                        if (is_array($arr) && count($arr)) {
                            $info[$key]['middleware'][0] = array_merge($info[$key]['middleware'][0], $arr);
                        }
                    }
                }
            }
            $GLOBALS['route_info'] = $info;
        }
    }

    public function names($names = false) {

        $info = end($GLOBALS['route_info']);
        $route = current($info);
        $module = strtolower(current($info['prefix']));

        $url = ltrim($module . '/' . trim($route, "/"), "/");
        #check if url is correct with valid parameters indicator
        if (!$this->_check_for_valid_url_with_parameters($url)) {
            echo "Warning: routing url is not valid with parameters indicator<br>Error:<br><pre>";
            print_r($url);
            echo "</pre>";
            die;
        }
        #check if there is only one parameters
        if (!$this->_check_url_for_single_optional_parameters_at_last($url)) {
            echo "Warning: routing url name '" . $names . "' is not allowed for parameterised url<br>Error:<br><pre>";
            print_r($url);
            echo "</pre>";
            die;
        }
        #now save routing name for later use
        $GLOBALS['route_name'][$names] = $url;
    }

    private function _check_url_for_single_optional_parameters_at_last($url) {

        $tot = preg_match_all('/[{}]/', $url, $matches);
        if ($tot == 0) {
            return true;
        }
        if ($tot != 2) {
            return false;
        } else {
            $tot = preg_match_all('/[?]/', $url, $matches);
            if ($tot > 1) {
                return false;
            } else if ($tot == 1) {
                $tot = preg_match_all('/\?}$/', $url, $matches);
                if ($tot == 1) {
                    return true;
                } else {
                    echo "Warning: routing optional parameters is not in correct format<br>Error:<br><pre>";
                    print_r($url);
                    echo "</pre>";
                    die;
                }
                return false;
            } else if ($tot == 0) {
                echo "Warning: routing url must not have any mandatory parameters<br>Error:<br><pre>";
                print_r($url);
                echo "</pre>";
                die;
            }
        }
        return true;
    }

    private function _check_for_valid_url_with_parameters($url) {
        $tot = preg_match_all('/[{}]/', $url, $matches);
        $result = $tot % 2 ? false : true;
        if ($result) {
            $tot = preg_match_all('/{}/', $url, $matches);
            if ($tot) {
                return false;
            }
            $tot = preg_match_all('/}{/', $url, $matches);
            if ($tot) {
                return false;
            }
            $tot = preg_match_all('/}\/{/', $url, $matches);
            if ($tot) {
                return false;
            }
            $tot = preg_match_all('/{{/', $url, $matches);
            if ($tot) {
                return false;
            }
            $tot = preg_match_all('/}}/', $url, $matches);
            if ($tot) {
                return false;
            }
        }
        return $result;
    }

    public static function error($arr = []) {
        @self::_save_routing_info($arr, ['', 'get', 'post', 'put', 'delete'], 'error');
        if (self::$_instance === null) {
            self::$_instance = new self;
        }
        return self::$_instance;
    }

    public static function get($arr = []) {
        @self::_save_routing_info($arr, ['get']);
        if (self::$_instance === null) {
            self::$_instance = new self;
        }
        return self::$_instance;
    }

    public static function post($arr = []) {
        @self::_save_routing_info($arr, ['post']);
        if (self::$_instance === null) {
            self::$_instance = new self;
        }
        return self::$_instance;
    }

    public static function put($arr = []) {
        @self::_save_routing_info($arr, ['put']);
        if (self::$_instance === null) {
            self::$_instance = new self;
        }
        return self::$_instance;
    }

    public static function delete($arr = []) {
        @self::_save_routing_info($arr, ['delete']);
        if (self::$_instance === null) {
            self::$_instance = new self;
        }
        return self::$_instance;
    }

    public static function any($arr = []) {
        @self::_save_routing_info($arr, ['', 'get', 'post', 'put', 'delete']);
        if (self::$_instance === null) {
            self::$_instance = new self;
        }
        return self::$_instance;
    }

    public static function normal($arr = []) {
        @self::_save_routing_info($arr, ['']);
        if (self::$_instance === null) {
            self::$_instance = new self;
        }
        return self::$_instance;
    }

    public static function match($arr = [], $match = []) {
        if (self::$_instance === null) {
            self::$_instance = new self;
        }

        if (!is_array($match)) {
            echo "Warning: routing second argument must be an array<br>Error:<br><pre>";
            print_r($match);
            echo "</pre>";
            die;
        }
        self::_save_routing_info($arr, $match);
        if (self::$_instance === null) {
            self::$_instance = new self;
        }
        return self::$_instance;
    }

    public static function group($info, $fun) {

        if (self::$_instance === null) {
            self::$_instance = new self;
        }

        if (!is_callable($fun)) {
            throw new Exception("Error Processing Request", 1);
        }
        if (!is_array($info)) {
            echo "Warning: group routing first argument must be an array<br>Error:<br><pre>";
            print_r($info);
            echo "</pre>";
            die;
        }
        $GLOBALS['is_group'] = true;
        $GLOBALS['route_group'] = $info;
        $fun();
        $GLOBALS['route_group'] = false;
        $GLOBALS['is_group'] = false;

        return self::$_instance;
    }

    private static function _save_routing_info($arr = array(), $type = false, $error = false) {
        #throw an error if first arguments are not an array
        if (!is_array($arr)) {
            die("Warning: routing arguments must be an array");
        }
        #throw error if elements of first arguments are less than 2
        if (count($arr) < 2) {
            echo "Warning: routing argument array must contain two or three elements<br>Error:<br><pre>";
            print_r($arr);
            echo "</pre>";
            die;
        }
        $module_flag = false;
        #now set module structure setting true indicate enabled false indicate disabled
        if (isset($arr[2]) && $arr[2]) {
            $module_flag = true;
        }
        #now set method type
        if (isset($arr[1])) {
            if ($type !== false) {
                $arr['method_type'] = $type;
            }
        }
        #now remove extra forward or backward slash from last
        if (isset($arr[0])) {
            $arr[0] = trim($arr[0], "/");
        }
        #now set middleware, prefix, module, url for route group function for later use
        $route_group = $GLOBALS['route_group'];
        $arr['url'] = '';
        if (isset($route_group['module']) && is_array($route_group['module'])) {
            $arr['module'] = isset($route_group['module']) ? $route_group['module'] : array();
        } else {
            $arr['module'][] = isset($route_group['module']) ? $route_group['module'] : false;
        }

        if (strtolower(trim($error)) === 'error') {
            $GLOBALS['route_error'] = current($arr);
        }

        #now check if already module is added and if not added then add new module if $module_flag is true
        if ($module_flag) {
            $mdl = trim(current($arr['module']));
            if (!$mdl && isset($arr[3])) {
                $arr['module'][0] = isset($arr[3]) ? $arr[3] : '';
            }
        }

        if (isset($route_group['module_structure'])) {
            $arr['module_structure'][] = $route_group['module_structure'];
        }

        #for the use of internal module purpose
        if (isset($route_group['internal'])) {
            $arr['internal'][] = $route_group['internal'];
        }

        $arr['prefix'][] = isset($route_group['prefix']) ? $route_group['prefix'] : false;
        $arr['middleware'][] = isset($route_group['middleware']) ? (is_array($route_group['middleware']) ? $route_group['middleware'] : [$route_group['middleware']]) : false;

        #now put above info in global variable for later use
        array_push($GLOBALS['route_info'], $arr);
    }
}

function __getdirlist($dir) {
    $list = __getdirlisting($dir);
    return $list;
}

function __getdirlisting($dir) {
    $root = scandir($dir);
    foreach ($root as $value) {
        if ($value === '.' || $value === '..') {
            continue;
        }

        if (is_file("$dir/$value")) {
            if (substr(trim($value), 0, 1) != '#') {
                $file = str_replace("//", '/', "$dir/$value");
                $result[] = $file;
                #include it now
                $ext = explode(".", $file);
                $ext = strtolower(end($ext));
                if ($ext === 'php') {
                    require_once($file);
                }
            }
            continue;
        }

        if (is_dir("$dir/$value")) {
            $file = str_replace("//", '/', "$dir/$value");
            $curdir = __getdirlisting($file);
            if (is_array($curdir)) {
                foreach ($curdir as $value) {
                    $result[] = $value;
                }
            }
        }
    }
    return isset($result) ? $result : false;
}

/*
 * Include all route files here
 */

$route_directory = APPPATH . 'routes/';
if (!is_dir($route_directory)) {
    echo "Warning: routes directory '" . $route_directory . "' not found";
    exit;
}

#include all route files here
__getdirlist($route_directory);

/*
 * Include all base-related route files here
 */

$base_route_directory = SYSTEMPATH . 'routes/';
if (is_dir($route_directory)) {
    #include all route files here
    __getdirlist($base_route_directory);
} 

