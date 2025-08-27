<?php

$_URI = new URI;

$url_info = _get_url_info($_URI);

if ($url_info !== false && (count($url_info) == 1)) {
    #get params of url if exists after routing url matched
    $params = _get_params_of_url(current($url_info), $_URI);
    #now set controller, function and module value
    $info = _set_controller_method_module_value($GLOBALS['route_info'][key($url_info)]);
    $module_structure = isset($GLOBALS['route_info'][key($url_info)]['module_structure']) ? $GLOBALS['route_info'][key($url_info)]['module_structure'] : array(MODULE_STRUCTURE);
    #set all possible values in on place
    $routingInfo = array(
        'route' => current($url_info),
        'controller' => $info->controller,
        'method' => $info->method,
        'method_type' => isset($GLOBALS['route_info'][key($url_info)]['method_type']) ? $GLOBALS['route_info'][key($url_info)]['method_type'] : '',
        'params' => $params,
        'is_module' => (boolean) (isset($GLOBALS['route_info'][key($url_info)][2]) ? $GLOBALS['route_info'][key($url_info)][2] : current($module_structure)),
        'module' => $info->module,
        'internal' => isset($GLOBALS['route_info'][key($url_info)]['internal']) ? 1 : 0,
        'middleware' => isset($GLOBALS['route_info'][key($url_info)]['middleware']) ? $GLOBALS['route_info'][key($url_info)]['middleware'] : array(),
        'library_path' => $GLOBALS['current_directory']
    );
    #for later use  in controller
    $GLOBALS['params'] = $params;
} else {
    #die("do here codeIgniter routing system working form url only because routes are not matched with url");
    if (!isset($GLOBALS['route_error'])) {
        die("Warning: routes are not matched!!");
    }

    __redirect($GLOBALS['route_error']);
}

function _get_url_info($URI) {
    $main_url = explode("/", isset($URI->uri) ? $URI->uri : '');
    #now go through route array and find the matched url information
    $matched_url = array();

    foreach ($GLOBALS['route_info'] as $key => $routing) {

        #$module = isset($routing['module'])?implode("/", $routing['module']):'';
        $prefix = isset($routing['prefix']) ? implode("/", $routing['prefix']) : '';
        #add moudle and then prefix and then routing url
        $route_url = trim($prefix . "/" . (isset($routing[0]) ? $routing[0] : ''), "/");
        #now convert route url into array
        $route_url_arr = explode("/", $route_url);
        #now match the possible url and catch it into an array to check later for ambiguity cases
        $matched = array();
        for ($i = 0; $i < count($route_url_arr); $i++) {
            $value = $route_url_arr[$i];
            if (_is_optional($value)) {
                $matched[] = $value;
                continue;
            }
            if (_is_parametrised($value)) {
                $matched[] = $value;
                continue;
            }
            if (isset($main_url[$i])) {
                if ($route_url_arr[$i] == $main_url[$i]) {
                    $matched[] = $value;
                }
            }
        }
        if (count($matched) == count($route_url_arr)) {
            $matched_url[$key] = implode("/", $matched);
        }
    }
    $re_matched_url = array();
    #check if matched url exists and have more than one matched
    if (count($matched_url) > 0) {
        #now recheck for existing url for any ambiguity and remove if possible
        foreach ($matched_url as $key => $row) {
            $matched = array();
            $row_arr = explode("/", $row);

            #check if all are matched
            if (count($row_arr) == count($main_url)) {
                $re_matched_url[$key] = $row;
            }
            #check if main url elements is less by 1 from routing elements
            else {
                if ((count($row_arr) - count($main_url)) === 1) {
                    #now check if last routing element is an optional element
                    if (_is_optional($row)) {
                        $re_matched_url[$key] = $row;
                    }
                }
                #check if main url elements is greater than from routing elements
                else if ((count($main_url) - count($row_arr)) > 0) {
                    #now check if last routing element is an any element
                    if (_is_any_parameter($row)) {
                        $re_matched_url[$key] = $row;
                    }
                }
            }
        }
    } else {
        return false;
    }
    if (count($re_matched_url) > 1) {
        echo "<b>Warning: your current url is...</b>'" . $URI->uri . "'<br>";
        echo "<b>which are making ambiguity with following routing url...</b><pre>";
        print_r($matched_url);
        echo "</pre>";
        die;
    }

    return $re_matched_url;
}

function _set_controller_method_module_value($url_info) {
    $controller_method = isset($url_info[1]) ? $url_info[1] : '';
    #check if controller info are valid
    $tot = preg_match_all('/[@]/', $controller_method, $matches);
    if ($tot > 1) {
        echo "Warning: routing controller path info are incorrect<br>Error:<br><pre>";
        print_r($controller_method);
        echo "</pre>";
        die;
    }
    $module = isset($url_info['module']) ? $url_info['module'] : '';
    $info = explode("@", $controller_method);
    $controller = trim((isset($info[0]) ? $info[0] : ''), "/");
    $method = array();

    if (is_array($url_info['method_type']) && count($url_info['method_type'])) {
        foreach ($url_info['method_type'] as $row) {
            $method[] = strtolower(trim(((isset($info[1]) ? $info[1] : 'index') . "" . $row), "_")); #restrict here method must be in lowercase
        }
    } else {
        $method[] = strtolower(trim(isset($info[1]) ? $info[1] : 'index'));
    }
    if (!count($method)) {
        $method[] = strtolower('index');
    }
    if (is_array($method) && isset($method[0]) && $method[0] == '') {
        $method[0] = strtolower('index');
    }
    $info = (object) array(
                'module' => $module,
                'controller' => $controller,
                'method' => $method
    );
    return $info;
}

function _reset_routingInfo_controller_nested_path($routingInfo) {

    $mpath = APPPATH;
    if (isset($routingInfo['internal']) && (boolean) $routingInfo['internal']) {
        $mpath = SYSTEMPATH;
    }

    $is_module = $routingInfo['is_module'];
    $module = trim(implode("/", $routingInfo['module']), "/");
    $controllers = explode("/", $routingInfo['controller']);
    $controllers_path = array();
    foreach ($controllers as $controller) {
        $controllers_path[] = 'controllers/' . $controller;
    }

    $cur_dir = array();
    for ($i = 0; $i < count($controllers_path) - 1; $i++) {
        $cur_dir[] = $controllers_path[$i];
    }
    $controllers_path = implode("/", $controllers_path) . ".php";
    $cur_dir = implode("/", $cur_dir);

    if ($is_module) {
        $controller = $mpath . 'modules/' . $module . '/' . $controllers_path;
        $cur_dir = $mpath . 'modules/' . $module . '/' . $cur_dir;
        $GLOBALS['internal-path'] = $mpath . 'modules/' . $module;
    } else {
        if ($module) {
            $controller = $mpath . $module . '/' . $controllers_path;
            $cur_dir = $mpath . $module . '/' . $cur_dir;
        } else {
            $module = 'controllers';
            if (count(explode("controllers", $controllers_path)) > 2) {
                $module = ''; #'controllers';
                $controller = $mpath . ltrim($controllers_path, "controllers/");
            } else {
                $controller = $mpath . ltrim($controllers_path);
            }
            $module = '';
            $cur_dir = $mpath . $module . $cur_dir;
        }
    }
    $current_directory = $cur_dir;
    $info = array(
        'current_directory' => $current_directory,
        'controller' => $controller
    );
    return $info;
}

function _get_params_of_url($url_info, $URI) {
    $url_info = explode("/", isset($url_info) ? $url_info : '');
    $main_url = explode("/", isset($URI->uri) ? $URI->uri : '');
    $params = array();

    for ($i = 0; $i < count($url_info); $i++) {
        $value = $url_info[$i];

        if (_is_any_parameter($value)) {
            for ($j = $i; $j < count($main_url); $j++) {
                $params[] = isset($main_url[$j]) ? $main_url[$j] : '';
            }
            break;
        }
        if (_is_parametrised($value)) {
            $value = preg_replace('/[{}]/', '', $value);
            $params[$value] = isset($main_url[$i]) ? $main_url[$i] : '';
            continue;
        }
        if (_is_optional($value)) {
            $value = preg_replace('/[{}]/', '', $value);
            $params[$value] = isset($main_url[$i]) ? $main_url[$i] : '';
            continue;
        }
    }
    return $params;
}

function _is_parametrised($str) {
    return preg_match_all('/[{}]/', $str, $matches);
}

function _is_optional($str) {
    return preg_match_all('/\?}$/', $str, $matches);
}

function _is_any_parameter($str) {
    return preg_match_all('/{any}$/', $str, $matches);
}

function __base_url($file_path = '') {
    if (isset($GLOBALS['route_name'][$file_path])) {
        $file_path = $GLOBALS['route_name'][trim($file_path)];
    }
    $base_url = $_SERVER['REQUEST_SCHEME'] . '://' . rtrim((isset($GLOBALS['base_url']) ? $GLOBALS['base_url'] : ''), "/") . "/" . ltrim($file_path, "/");
    return $base_url;
}

function __redirect($path = '') {
    $url = __base_url($path);
    header('Location:' . $url);
    exit;
}

$info = _reset_routingInfo_controller_nested_path($routingInfo);

$GLOBALS['routingInfo'] = $routingInfo;
$GLOBALS['current_directory'] = $info['current_directory'];

$GLOBALS['controller'] = $info['controller'];
