<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors',1);
error_reporting(E_ALL);

header('Access-Control-Allow-Origin: *');
ini_set('memory_limit','1024M');
ini_set('max_execution_time', 800);
gc_enabled();
gc_collect_cycles();
/*
 *
 * CHANGE FOLLOWING FOR AS PER DIRECTORY STRUCTURE
 *
 */
const MAIN = '';
const MAIN_DIRECTORY = 'webapps';
const APP_DIRECTORY = 'apps';
const SYSTEM_DIRECTORY = 'base';
const QUERYLOGGER = false;

define('MODULE_STRUCTURE', FALSE);

#set timezone for current server
date_default_timezone_set('UTC'); #date_default_timezone_set('Asia/Kolkata');

/*
 * NEXO FRAMEWORK ERROR HANDLING CONFIGURATION
 */
define('NEXO_ENVIRONMENT', 'development'); // 'development' or 'production'
define('NEXO_LOG_ERRORS', true); // Enable error logging

/*
 * Create logs directory if it doesn't exist
 */
if (NEXO_LOG_ERRORS && !is_dir(MAIN . MAIN_DIRECTORY . '/' . APP_DIRECTORY . '/logs')) {
    mkdir(MAIN . MAIN_DIRECTORY . '/' . APP_DIRECTORY . '/logs', 0777, true);
}

/*
 * Set log file path if error logging is enabled
 */
if (NEXO_LOG_ERRORS) {
    define('NEXO_LOG_FILE', MAIN . MAIN_DIRECTORY . '/' . APP_DIRECTORY . '/logs/nexo_errors.log');
}

/*
 *
 * include framework main class NEXO
 *
 */
define('APPPATH', MAIN . MAIN_DIRECTORY . '/' . APP_DIRECTORY . '/');
define('SYSTEMPATH', MAIN . MAIN_DIRECTORY . '/' . SYSTEM_DIRECTORY . '/');


require_once(SYSTEMPATH . 'bind/nexo.php'); 
