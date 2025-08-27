<?php

class url implements igurl {

    public $params;
    public $base_url;
    public $document_root;
    public $server;
    public $useragent;

    function __construct() {
        $this->params = $GLOBALS['params'];
        $this->base_url = $this->baseurl();
        $this->document_root = $this->documentroot();
        $this->server = $this->server();
        $this->useragent = $this->useragent();
    }

    public function currentdir($filepath, $split = false) {
        list($f, $l) = explode($this->currentroot(), $filepath);
        if ($split) {
            list($f, $l) = explode($split, $l);
            return $l;
        }
        return $l;
    }

    public function currentroot($path = '') {
        $cpath = $GLOBALS['info']['current_directory'];
        if (!empty($path)) {
            $cpath = $cpath . $path;
        }
        return $cpath;
    }

    public function params() {
        $params = array();
        foreach ($this->params as $key => $value) {
            $params[$key] = urldecode($value);
        }
        return $params;
    }

    public function fileurl($file_path = '') {
        return $file_path;
    }

    public function baseurl($file_path = '') {
        if (strstr($file_path, 'http')) {
            return $file_path;
        }
        if (isset($GLOBALS['route_name'][$file_path])) {
            $file_path = $GLOBALS['route_name'][trim($file_path)];
        }

        $base_url = rtrim((isset($GLOBALS['base_url']) ? $GLOBALS['base_url'] : ''), "/") . "/" . ltrim($file_path, "/");
        return $base_url;
    }

    public function documentroot($file_path = '') {
        $file_path = (isset($GLOBALS['document_url']) ? $GLOBALS['document_url'] : '') . "/" . ltrim($file_path, "/");
        return $file_path;
    }

    public function redirect($path = '') {
        if (!strstr($path, 'http')) {
            $path = $this->baseurl($path);
        }
        header('Location:' . $path);
        exit;
    }

    public function useragent() {
        return isset($GLOBALS['_SERVER']['HTTP_USER_AGENT']) ? $GLOBALS['_SERVER']['HTTP_USER_AGENT'] : '';
    }

    public function iscurrenturl($url_path = '') {
        if (strtolower(trim($GLOBALS['routingInfo']['route'])) === strtolower(trim($url_path))) {
            return true;
        }
        return false;
    }

    public function currenturl() {
        $url = $this->baseurl($GLOBALS['_URI']->uri);
        return $url;
    }

    public function redirectback() {
        $this->redirect($GLOBALS['_SERVER']['HTTP_REFERER']);
    }

    public function server() {
        return (object) $GLOBALS['_SERVER'];
    }
}
