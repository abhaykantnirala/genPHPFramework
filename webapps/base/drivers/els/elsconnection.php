<?php

class elsconnection {

    protected $QUERY_BUILDER;
    protected $DNS;
    protected $PORT;
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
    protected $url = '';

    function __construct() {
        $this->QUERY_BUILDER = $GLOBALS['DB']['els']['QUERY_BUILDER'] ?? 1;
        $this->DNS = rawurldecode($GLOBALS['DB']['els']['DNS']) ?? '';
        $this->PORT = rawurldecode($GLOBALS['DB']['els']['PORT']) ?? '';
        $this->HOSTNAME = rawurldecode($GLOBALS['DB']['els']['HOSTNAME']) ?? 'localhost';
        $this->USERNAME = rawurldecode($GLOBALS['DB']['els']['USERNAME']) ?? 'root';
        $this->PASSWORD = rawurldecode($GLOBALS['DB']['els']['PASSWORD']) ?? '';
        $this->DATABASE = rawurldecode($GLOBALS['DB']['els']['DATABASE']) ?? '';
        $this->DB_DRIVER = rawurldecode($GLOBALS['DB']['els']['DB_DRIVER']) ?? 'mysqli';
        $this->DB_PREFIX = rawurldecode($GLOBALS['DB']['els']['DB_PREFIX']) ?? '';
        $this->PCONNECT = $GLOBALS['DB']['els']['PCONNECT'] ?? FALSE;
        $this->DB_DEBUG = $GLOBALS['DB']['els']['DB_DEBUG'] ?? FALSE;
        $this->CACHE_ON = $GLOBALS['DB']['els']['CACHE_ON'] ?? FALSE;
        $this->CACHE_DIR = rawurldecode($GLOBALS['DB']['els']['CACHE_DIR']) ?? '';
        $this->CHAR_SET = rawurldecode($GLOBALS['DB']['els']['CHAR_SET']) ?? 'utf8';
        $this->DBCOLLAT = rawurldecode($GLOBALS['DB']['els']['DBCOLLAT']) ?? 'utf8_general_ci';
        $this->SWAP_PRE = rawurldecode($GLOBALS['DB']['els']['SWAP_PRE']) ?? '';
        $this->ENCRYPT = $GLOBALS['DB']['els']['ENCRYPT'] ?? FALSE;
        $this->COMPRESS = $GLOBALS['DB']['els']['COMPRESS'] ?? FALSE;
        $this->STRICT_ON = $GLOBALS['DB']['els']['STRICT_ON'] ?? FALSE;
        $this->FAILOVER = $GLOBALS['DB']['els']['FAILOVER'] ?? array();
        $this->SAVE_QUERIES = $GLOBALS['DB']['els']['SAVE_QUERIES'] ?? TRUE;

        if (preg_match('/^(https:\/\/|http:\/\/)/i', $this->HOSTNAME, $m)) {
            $this->url = implode('', array($this->HOSTNAME, empty($this->PORT) ? '' : ':' . $this->PORT, '/'));
        } else {
            $this->url = implode('', array('http://', $this->HOSTNAME, empty($this->PORT) ? '' : ':' . $this->PORT, '/'));
        }

        /*
         * 
         * Rename database if dbprefix exists 
         *
         */

        $this->DATABASE = empty(trim($this->DB_PREFIX)) ? $this->DATABASE : ($this->DB_PREFIX . '_' . $this->DATABASE);

        /*
         * 
         * Check if database name exists otherwise throw error
         *
         */
        if (empty($this->DATABASE)) {
            echo "Warning: Database name required";
            exit(0);
        }
    }

}
