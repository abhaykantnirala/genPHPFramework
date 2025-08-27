<?php

class mysqliPDOconnection {

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
    protected $CONNECTION;

    function __construct() {

        $this->QUERY_BUILDER = $GLOBALS['DB']['mysqliPDO']['QUERY_BUILDER'] ?? 1;
        $this->DNS = rawurldecode($GLOBALS['DB']['mysqliPDO']['DNS']) ?? '';
        $this->HOSTNAME = rawurldecode($GLOBALS['DB']['mysqliPDO']['HOSTNAME']) ?? 'localhost';
        $this->USERNAME = rawurldecode($GLOBALS['DB']['mysqliPDO']['USERNAME']) ?? 'root';
        $this->PASSWORD = rawurldecode($GLOBALS['DB']['mysqliPDO']['PASSWORD']) ?? '';
        $this->DATABASE = rawurldecode($GLOBALS['DB']['mysqliPDO']['DATABASE']) ?? '';
        $this->DB_DRIVER = rawurldecode($GLOBALS['DB']['mysqliPDO']['DB_DRIVER']) ?? 'mysqli';
        $this->DB_PREFIX = rawurldecode($GLOBALS['DB']['mysqliPDO']['DB_PREFIX']) ?? '';
        $this->PCONNECT = $GLOBALS['DB']['mysqliPDO']['PCONNECT'] ?? FALSE;
        $this->DB_DEBUG = $GLOBALS['DB']['mysqliPDO']['DB_DEBUG'] ?? FALSE;
        $this->CACHE_ON = $GLOBALS['DB']['mysqliPDO']['CACHE_ON'] ?? FALSE;
        $this->CACHE_DIR = rawurldecode($GLOBALS['DB']['mysqliPDO']['CACHE_DIR']) ?? '';
        $this->CHAR_SET = rawurldecode($GLOBALS['DB']['mysqliPDO']['CHAR_SET']) ?? 'utf8';
        $this->DBCOLLAT = rawurldecode($GLOBALS['DB']['mysqliPDO']['DBCOLLAT']) ?? 'utf8_general_ci';
        $this->SWAP_PRE = rawurldecode($GLOBALS['DB']['mysqliPDO']['SWAP_PRE']) ?? '';
        $this->ENCRYPT = $GLOBALS['DB']['mysqliPDO']['ENCRYPT'] ?? FALSE;
        $this->COMPRESS = $GLOBALS['DB']['mysqliPDO']['COMPRESS'] ?? FALSE;
        $this->STRICT_ON = $GLOBALS['DB']['mysqliPDO']['STRICT_ON'] ?? FALSE;
        $this->FAILOVER = $GLOBALS['DB']['mysqliPDO']['FAILOVER'] ?? array();
        $this->SAVE_QUERIES = $GLOBALS['DB']['mysqliPDO']['SAVE_QUERIES'] ?? TRUE;

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

        $this->_connect();
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
        //$this->CONNECTION = @new mysqli($this->HOSTNAME, $this->USERNAME, $this->PASSWORD, $this->DATABASE);
        //$this->CONNECTION = @new PDO($this->HOSTNAME, $this->USERNAME, $this->PASSWORD, $this->DATABASE);

        try {
            $this->CONNECTION = @new PDO("mysql:host=" . $this->HOSTNAME . ";dbname=" . $this->DATABASE, $this->USERNAME, $this->PASSWORD);
        } catch (PDOException $e) {
            if ($this->DB_DEBUG === TRUE || $this->DB_DEBUG === true) {
                echo '<h1>Database Error</h1>';
                echo '<h4><span style="color:red;">Error: </span>' . $e->getMessage() . '</h4>';
                exit(3);
            }
        }


        $this->CONNECTION->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

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
}