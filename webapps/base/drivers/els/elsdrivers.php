<?php

if (file_exists(SYSTEMPATH . 'drivers/' . $GLOBALS['DB']['els']['DB_DRIVER'] . '/elsconnection.php')) {
    require_once(SYSTEMPATH . 'drivers/' . $GLOBALS['DB']['els']['DB_DRIVER'] . '/elsconnection.php');
}

class elsdrivers extends elsconnection {

    protected $_result;
    protected $_header;

    function __construct() {
        parent::__construct();
    }

    /*
     * params: array($method='GET', $maincommand=[required], $data=[json data, OPTIONAL] );
     *
     * */

    protected function _query($cmdinfo = array(), $return = true) {
        $this->_header = array('Content-Type:application/json');
        if(!empty($this->PASSWORD) && !empty($this->USERNAME)){
            $this->_header = array('Content-Type:application/json',"authorization: Basic ".base64_encode($this->USERNAME.':'.$this->PASSWORD));
        }
        if (!count($cmdinfo)) {
            die('command info required');
        }
        /*
         * Check heading info
         */
        if (!$cmdinfo['maincommand']) {
            die('main command required');
        }

        $method = isset($cmdinfo['method']) ? $cmdinfo['method'] : 'GET';
        $maincommand = $cmdinfo['maincommand'];
        $data = isset($cmdinfo['data']) ? (is_array($cmdinfo['data']) ? json_encode($cmdinfo['data']) : $cmdinfo['data']) : '';
        $header = $this->_header;
        $weburl = $this->url . $maincommand;

        #create object
        require_once (SYSTEMPATH . 'bind/' . 'curlcontroller.php');
        $curlcontroller = new curlcontroller();
        #invoke curl for current request
        $info = array(
            'url' => $weburl,
            'method' => $method,
            'data' => $data,
            'header' => $header,
            'return' => $return
        );
        $this->_result = $curlcontroller->webcontent($info);

        #$this->_result = $this->webcontent();
    }

    protected function _get_insert_id() {
        return mysqli_insert_id($this->CONNECTION);
    }

    protected function _rowaffected() {
        return mysqli_affected_rows($this->CONNECTION);
    }

    protected function _get_data() {
        return $this->_result;
    }

}
