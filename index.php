<?php

// 🔒 SECURE SESSION CONFIGURATION (Must be first - before any output/headers)
if (!session_id()) {
    // Security: Prevent session hijacking and fixation attacks
    ini_set('session.cookie_httponly', '1');        // Prevent XSS attacks via JavaScript
    ini_set('session.cookie_secure', '1');          // Only send over HTTPS (auto-detects)
    ini_set('session.use_strict_mode', '1');        // Prevent session fixation attacks
    ini_set('session.cookie_samesite', 'Strict');   // CSRF protection
    ini_set('session.gc_maxlifetime', '1440');      // Session timeout (24 minutes)
    ini_set('session.gc_probability', '1');         // Garbage collection probability
    ini_set('session.gc_divisor', '100');           // Garbage collection divisor (1% chance)
    
    // Generate secure session ID
    session_start();
    
    // Regenerate session ID periodically for security
    if (!isset($_SESSION['created'])) {
        $_SESSION['created'] = time();
    } elseif (time() - $_SESSION['created'] > 1800) { // 30 minutes
        session_regenerate_id(true);
        $_SESSION['created'] = time();
    }
}

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
