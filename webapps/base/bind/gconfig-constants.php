<?php

function __configs() {
    $config = array();
    $configs[] = $GLOBALS['current_directory'] . 'configs/config.php';
    $configs[] = APPPATH . 'configs/config.php';
    foreach ($configs as $file) {
        if (file_exists($file)) {
            require_once($file);
        }
    }
    return $config;
}

function __constants() {
    $constants = array();
    $constants[] = $GLOBALS['current_directory'] . 'configs/constant.php';
    $constants[] = APPPATH . 'configs/constant.php';
    foreach ($constants as $file) {
        if (file_exists($file)) {
            require_once($file);
        }
    }
}

__constants();
$GLOBALS['g-configs'] = __configs();
