<?php

class mysqliconnection {

    protected $QUERY_BUILDER;
    protected $DNS;
    protected $HOSTNAME;
    protected $USERNAME;
    protected $PASSWORD;
    protected $DATABASE;
    protected $DB_DRIVER;
    protected $DB_PREFIX;
    protected $PCONNECT;
    protected $DB_DEBUG;
    protected $CACHE_ON;
    protected $CACHE_DIR;
    protected $CHAR_SET;
    protected $DBCOLLAT;
    protected $SWAP_PRE;
    protected $ENCRYPT;
    protected $COMPRESS;
    protected $STRICT_ON;
    protected $FAILOVER;
    protected $SAVE_QUERIES;
    protected $CONNECTION_ID;

    function __construct() {

        $this->QUERY_BUILDER = isset($GLOBALS['DB']['mysqli']['QUERY_BUILDER']) ? $GLOBALS['DB']['mysqli']['QUERY_BUILDER'] : 1;
        $this->DNS = rawurldecode(isset($GLOBALS['DB']['mysqli']['DNS']) ? $GLOBALS['DB']['mysqli']['DNS'] : '');
        $this->HOSTNAME = rawurldecode(isset($GLOBALS['DB']['mysqli']['HOSTNAME']) ? $GLOBALS['DB']['mysqli']['HOSTNAME'] : 'localhost');
        $this->USERNAME = rawurldecode(isset($GLOBALS['DB']['mysqli']['USERNAME']) ? $GLOBALS['DB']['mysqli']['USERNAME'] : 'root');
        $this->PASSWORD = rawurldecode(isset($GLOBALS['DB']['mysqli']['PASSWORD']) ? $GLOBALS['DB']['mysqli']['PASSWORD'] : '');
        $this->DATABASE = rawurldecode(isset($GLOBALS['DB']['mysqli']['DATABASE']) ? $GLOBALS['DB']['mysqli']['DATABASE'] : '');
        $this->DB_DRIVER = rawurldecode(isset($GLOBALS['DB']['mysqli']['DB_DRIVER']) ? $GLOBALS['DB']['mysqli']['DB_DRIVER'] : 'mysqli');
        $this->DB_PREFIX = rawurldecode(isset($GLOBALS['DB']['mysqli']['DB_PREFIX']) ? $GLOBALS['DB']['mysqli']['DB_PREFIX'] : '');
        $this->PCONNECT = isset($GLOBALS['DB']['mysqli']['PCONNECT']) ? $GLOBALS['DB']['mysqli']['PCONNECT'] : FALSE;
        $this->DB_DEBUG = isset($GLOBALS['DB']['mysqli']['DB_DEBUG']) ? $GLOBALS['DB']['mysqli']['DB_DEBUG'] : FALSE;
        $this->CACHE_ON = isset($GLOBALS['DB']['mysqli']['CACHE_ON']) ? $GLOBALS['DB']['mysqli']['CACHE_ON'] : FALSE;
        $this->CACHE_DIR = rawurldecode(isset($GLOBALS['DB']['mysqli']['CACHE_DIR']) ? $GLOBALS['DB']['mysqli']['CACHE_DIR'] : '');
        $this->CHAR_SET = rawurldecode(isset($GLOBALS['DB']['mysqli']['CHAR_SET']) ? $GLOBALS['DB']['mysqli']['CHAR_SET'] : 'utf8');
        $this->DBCOLLAT = rawurldecode(isset($GLOBALS['DB']['mysqli']['DBCOLLAT']) ? $GLOBALS['DB']['mysqli']['DBCOLLAT'] : 'utf8_general_ci');
        $this->SWAP_PRE = rawurldecode(isset($GLOBALS['DB']['mysqli']['SWAP_PRE']) ? $GLOBALS['DB']['mysqli']['SWAP_PRE'] : '');
        $this->ENCRYPT = isset($GLOBALS['DB']['mysqli']['ENCRYPT']) ? $GLOBALS['DB']['mysqli']['ENCRYPT'] : FALSE;
        $this->COMPRESS = isset($GLOBALS['DB']['mysqli']['COMPRESS']) ? $GLOBALS['DB']['mysqli']['COMPRESS'] : FALSE;
        $this->STRICT_ON = isset($GLOBALS['DB']['mysqli']['STRICT_ON']) ? $GLOBALS['DB']['mysqli']['STRICT_ON'] : FALSE;
        $this->FAILOVER = isset($GLOBALS['DB']['mysqli']['FAILOVER']) ? $GLOBALS['DB']['mysqli']['FAILOVER'] : array();
        $this->SAVE_QUERIES = isset($GLOBALS['DB']['mysqli']['SAVE_QUERIES']) ? $GLOBALS['DB']['mysqli']['SAVE_QUERIES'] : TRUE;

        /*
         * 
         * Rename database if dbprefix exists
         *
         */

        $this->DATABASE = empty(trim($this->DB_PREFIX)) ? $this->DATABASE : ($this->DB_PREFIX . '_' . $this->DATABASE);

        /*
         * 
         * create database connection now
         *
         */
    }

    protected function _connect() {
        /*
         * 
         * Check if database name exists otherwise throw error
         *
         */
        if (empty($this->DATABASE)) {
            echo "Warning: Database name required";
            exit(0);
        }
        /*
         * 
         * now try to crate connection 
         *
         */
        @$this->CONNECTION = @new mysqli($this->HOSTNAME, $this->USERNAME, $this->PASSWORD, $this->DATABASE);

        /*
         * 
         * Check for error and throw same if found
         *
         */
        if ($this->CONNECTION->connect_error) {
            echo '<h1>Database Error</h1>';
            echo '<h4><span style="color:red;">Error: </span>' . $this->CONNECTION->connect_error . '</h4>';
            exit(3);
        }

        $this->_set_mysqli_setting_values();
    }

    private function _set_mysqli_setting_values() {
        /*
         * 
         * Set charset
         * Set dbcollat
         *
         */

        $Query = "SET NAMES '" . $this->CHAR_SET . "' COLLATE '" . $this->DBCOLLAT . "';";
        mysqli_query($this->CONNECTION, $Query);

        /*
         * 
         * Set strict mode
         *
         */

        $Query = "SET global sql_mode='" . (($this->STRICT_ON === TRUE) ? 'STRICT_TRANS_TABLES' : '') . "';";
        #mysqli_query($this->CONNECTION, $Query);


        /*
         * 
         * Throw error if catched AND DB_DEBUG is true
         *
         */

        if ($this->DB_DEBUG === TRUE) {
            $Error = mysqli_error($this->CONNECTION);
            if ($Error) {
                $Error = print_r($Error, TRUE);
                echo '<h1>Database Error</h1>';
                echo '<h4><span style="color:red;">Error: </span>' . $Error . '</h4>';
                exit(3);
            }
        }
    }

    function __destruct() {
        #close connection
        if (!empty($this->CONNECTION)) {
            if (!is_null($this->CONNECTION)) {
                // @mysqli_close($this->CONNECTION);
            }
        }
    }
}
