<?php

/**
 * session handling library
 */
class session implements igsession {

    public $sessionid;
    public $cdir;

    function __construct() {
        /*
         *
         * FOR UNIQUE SESSION ID FOR EACH PROJECT SO THAT IT CAN'T BE INFLUENCED BY OTHER PROJECT SESSIONS and urls
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

        $this->cdir = $GLOBALS['info']['current_directory'];

        if ($flag != -1) {
            $base_url = base64_encode(rtrim(isset($GLOBALS['base_url']) ? $GLOBALS['base_url'] : '', '/'));
            $this->sessionid = bin2hex($base_url . base64_encode($flag));
        } else {
            $base_url = base64_encode(rtrim(isset($GLOBALS['base_url']) ? $GLOBALS['base_url'] : '', '/'));
            $this->sessionid = bin2hex($base_url . base64_encode(str_replace("/", "-", rtrim(current(explode("controllers", $GLOBALS['current_directory'])), "/"))));
        }
    }

    public function setmaxlife($max_life_value) {
        if (!is_numeric($max_life_value)) {
            echo 'Invalid value for session maxlifetime';
            exit(0);
        }
        @ini_set('session.gc_maxlifetime', $max_life_value);
    }

    public function getmaxlife() {
        return ini_get('session.gc_maxlifetime');
    }

    public function getdata($session_key = '') {
        return isset($_SESSION[$this->sessionid][$session_key]) ? $_SESSION[$this->sessionid][$session_key] : '';
    }

    public function setdata($session_key, $session_value = NULL) {
        $_SESSION[$this->sessionid][$session_key] = $session_value;
    }

    public function unsetdata($session_key) {
        unset($_SESSION[$this->sessionid][$session_key]);
    }

    public function flashdata($session_key) {
        $flash_data = $this->getdata($session_key);
        $this->unsetdata($session_key);
        return $flash_data;
    }

    public function getalldata() {
        return isset($_SESSION[$this->sessionid]) ? $_SESSION[$this->sessionid] : '';
    }

    public function unsetalldata() {
        $all_session = $this->getalldata();
        foreach ($all_session as $session_key => $session_value) {
            $this->unsetdata($session_key);
        }
        @session_destroy();
    }

    /* custom session (file format) */

    public function setcdata($session_key, $session_value = NULL) {
        $filepath = (isset($GLOBALS['document_url']) ? $GLOBALS['document_url'] : '') . "/" . $this->cdir . 'resources/session';
        $filepath .= '/' . md5($session_key);
        $this->save($filepath, $session_value);
    }

    public function getcdata($session_key = '') {
        $filepath = (isset($GLOBALS['document_url']) ? $GLOBALS['document_url'] : '') . "/" . $this->cdir . 'resources/session';
        $filepath .= '/' . md5($session_key);
        $res = false;
        if (file_exists($filepath)) {
            $res = @file_get_contents($filepath);
        }
        return $res;
    }

    public function unsetcdata($session_key) {
        $filepath = (isset($GLOBALS['document_url']) ? $GLOBALS['document_url'] : '') . "/" . $this->cdir . 'resources/session';
        $filepath .= '/' . md5($session_key);
        @file_put_contents($filepath, '');
    }

    public function unsetsession($sessionpath, $session_key) {
        $sessionpath .= '/' . md5($session_key);
        $old = umask(0);
        chmod($sessionpath, 0777);
        file_put_contents($sessionpath, '');
        umask($old);
    }

    public function flashcdata($session_key) {
        $flash_data = $this->getcdata($session_key);
        $this->unsetcdata($session_key);
        return $flash_data;
    }

    public function getcutime($session_key) {
        $filepath = (isset($GLOBALS['document_url']) ? $GLOBALS['document_url'] : '') . "/" . $this->cdir . 'resources/session';
        $filepath .= '/' . md5($session_key);
        if (file_exists($filepath)) {
            return filemtime($filepath);
        }
        return 0;
    }

    private function save($path, $data, $append = false) {
        $cpath = explode('/', str_replace('//', '/', $path));
        $file = end($cpath);
        unset($cpath[count($cpath) - 1]);
        #try to create directory
        $cpath = implode('/', $cpath);

        if (!empty($cpath) && !$this->createdir($cpath)) {
            die('Unable to create directory <h2>' . $cpath . '</h2>');
        }

        #move ahead to create file
        $filename = $path;
        $error = false;
        if ($append) {
            file_put_contents($filename, $data, FILE_APPEND);
        } else {
            file_put_contents($filename, $data);
        }

        if ($error) {
            die($error);
        }

        return true;
    }

    private function createdir($dirpath) {
        #check if directory exists
        if (!is_dir($dirpath)) {
            #try to create directory
            return mkdir($dirpath, 0777, true);
        } else {
            return true;
        }
    }
}
