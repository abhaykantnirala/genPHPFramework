<?php

if (file_exists(SYSTEMPATH . 'drivers/' . $GLOBALS['DB']['mysqli']['DB_DRIVER'] . '/mysqliconnection.php')) {
    require_once(SYSTEMPATH . 'drivers/' . $GLOBALS['DB']['mysqli']['DB_DRIVER'] . '/mysqliconnection.php');
}

class mysqlidrivers extends mysqliconnection {

    protected $_dquery;

    function __construct() {
        parent::__construct();
    }

    private function baseurl($file_path = '') {
        if (isset($GLOBALS['route_name'][$file_path])) {
            $file_path = $GLOBALS['route_name'][trim($file_path)];
        }

        $base_url = rtrim((isset($GLOBALS['base_url'])?$GLOBALS['base_url']: ''), "/") . "/" . ltrim($file_path, "/");
        return $base_url;
    }

    private function savequery($mysql_command) {
        require_once (SYSTEMPATH . 'bind/' . 'curlcontroller.php');
        $curlcontroller = new curlcontroller();

        $info = array(
            'url' => $this->baseurl('sql-query-logger'),
            'method' => 'put',
            'data' => $mysql_command,
            'return' => false
        );
        $curlcontroller->webcontent($info);
    }

    protected function _query($mysql_command) {

        $this->_dquery = mysqli_query($this->CONNECTION, $mysql_command);

        /*
          if (QUERYLOGGER) {
          #save query to database
          $this->savequery($mysql_command);
          }
         */

        #show error on query
        if ($this->DB_DEBUG === TRUE || $this->DB_DEBUG === true) {
            if ($Error = mysqli_error($this->CONNECTION)) {
                echo '<h1>Database Error</h1>';
                echo '<h4><span style="color:red;">Error: </span>' . $Error . '</h4>';
                echo '<h4><span style="color:blue;">Query: </span>' . $mysql_command . '</h4>';
                exit(3);
            }
        }
    }

    protected function _get_insert_id() {
        return mysqli_insert_id($this->CONNECTION);
    }

    protected function _rowaffected() {
        return mysqli_affected_rows($this->CONNECTION);
    }

    protected function _get_assoc_data($query = 0) {
        #for first row output
        if ($query == 1) {
            return mysqli_fetch_assoc($this->_dquery);
        }

        #for all record
        $result = array();
        while ($row = mysqli_fetch_assoc($this->_dquery)) {
            $result[] = $row;
        }
        return $result;
    }

    protected function _get_object_data($query = 0) {
        #for first row output
        if ($query == 1) {
            return mysqli_fetch_object($this->_dquery);
        }

        #for all record
        $result = array();
        while ($row = mysqli_fetch_object($this->_dquery)) {
            $result[] = $row;
        }
        return $result;
    }

}
