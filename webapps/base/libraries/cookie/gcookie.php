<?php

/**
 * session handling library
 */
class cookie implements igcookie {

    private $cookie_id;
    private $cookie_name;
    private $delimiter = '^-^-^';
    public $sessiondefaultmaxtime = 86400 * 30; // 86400 = 1 day => total for 1 month

    function __construct() {
        /*
         *
         * FOR UNIQUE COOKIE ID FOR EACH PROJECT SO THAT IT CAN'T BE INFLUENCED BY OTHER PROJECT SESSIONS
         */

        $flag = -1;
        if (isset($GLOBALS['sgroup'])) {
            $path = rtrim(current(explode("controllers", $GLOBALS['current_directory'])), "/");
            $module = explode('/', $path);
            $module = end($module);
            #search if $module exists in sgroup
            foreach ($GLOBALS['sgroup'] as $key => $rows) {
                if (isset($rows[$module])) {
                    $flag = $key;
                }
            }
        }

        if ($flag != -1) {
            $base_url = base64_encode(rtrim(isset($GLOBALS['base_url']) ? $GLOBALS['base_url'] : '', '/'));
            $this->cookie_id = bin2hex($base_url . base64_encode($flag));
        } else {
            $base_url = base64_encode(rtrim(isset($GLOBALS['base_url']) ? $GLOBALS['base_url'] : '', '/'));
            $this->cookie_id = bin2hex($base_url . base64_encode(str_replace("/", "-", rtrim(current(explode("controllers", $GLOBALS['current_directory'])), "/"))));
        }
    }

    public function setmaxlife($max_life_value) {
        if (!is_numeric($max_life_value)) {
            echo 'Invalid value for session maxlifetime';
            exit(0);
        }
        $this->sessiondefaultmaxtime = $max_life_value;
    }

    public function getmaxlife() {
        return $this->sessiondefaultmaxtime;
    }

    public function getdata($cookie_key) {
        $this->cookie_name = $this->cookie_id . $this->delimiter . $cookie_key;
        return isset($_COOKIE[$this->cookie_name]) ? $_COOKIE[$this->cookie_name] : '';
    }

    public function setdata($cookie_key, $cookie_value = NULL) {
        $this->cookie_name = $this->cookie_id . $this->delimiter . $cookie_key;
        setcookie($this->cookie_name, $cookie_value, (time() + $this->sessiondefaultmaxtime), "/", "", 0);
    }

    public function unsetdata($cookie_key) {
        $this->cookie_name = $this->cookie_id . $this->delimiter . $cookie_key;
        setcookie($this->cookie_name, '', (time() - 100), "/", "", 0);
    }

    public function flashdata($cookie_key) {
        $cookie = $this->getdata($cookie_key);
        $this->unsetdata($cookie_key);
        return $cookie;
    }

    public function getalldata() {
        $cookies = $_COOKIE;
        $cookies_array = [];
        foreach ($cookies as $cookie_name => $cookie_value) {
            if ($cookie_name === 'PHPSESSID') {
                $cookies_array['PHPSESSID'] = $cookie_value;
            }
            if (strstr($cookie_name, $this->cookie_id . $this->delimiter)) {
                $name = explode($this->cookie_id . $this->delimiter, $cookie_name);
                $cookies_array[end($name)] = $cookie_value;
            }
        }
        return $cookies_array;
    }

    public function unsetalldata() {
        $cookies = $_COOKIE;
        foreach ($cookies as $cookie_name => $cookie_value) {
            if ($cookie_name === 'PHPSESSID') {
                continue;
            }
            if (strstr($cookie_name, $this->cookie_id)) {
                setcookie($cookie_name, '', (time() - 100), "/", "", 0);
            }
        }
    }
}
