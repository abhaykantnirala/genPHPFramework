<?php

class input implements iginput {

    public $data;
    private $requesttype = 'GET';

    function __construct() {
        $this->data = explode("?", $GLOBALS['_SERVER']['REQUEST_URI']);
        $this->data = isset($this->data[1]) ? $this->data[1] : '';
        $this->requesttype = $GLOBALS['request_method'];
    }

    public function header($key = '') {
        $headers = getallheaders();
        if (trim($key)) {
            $data = isset($headers[$key]) ? $headers[$key] : '';
        } else {
            $data = isset($headers) ? $headers : array();
        }
        return $data;
    }

    public function get($key = '') {
        $data = array();
        $get = array();

        if (count($_GET)) {
            $data = $_GET;
        } else {
            if (strstr($this->data, "&")) {
                $data = explode("&", $this->data);
            } else {
                $data[] = $this->data;
            }
        }

        foreach ($data as $value) {
            $value = explode("=", $value);
            if (current($value)) {
                $get[current($value)] = end($value);
            }
        }

        if (trim($key)) {
            $get = isset($get[$key]) ? $get[$key] : '';
        }
        return $get;
    }

    public function post($key = '') {
        if (trim($key)) {
            $data = isset($_POST[$key]) ? $_POST[$key] : '';
        } else {
            $data = isset($_POST) ? $_POST : array();
        }
        return $data;
    }

    public function put() {
        $data = file_get_contents("php://input");
        return $data;
    }

    public function file($key = '') {
        if (isset($_FILES)) {
            $file = $_FILES;
            if (!empty($key)) {
                $file = $_FILES[$key];
            }
            return $file;
        }
        return FALSE;
    }
}
