<?php

if (file_exists(APPPATH . 'configs/database.php')) {
    require_once(APPPATH . 'configs/database.php');
} else {
    echo "Warning: database.php file does not exist in configs directory";
    exit(3);
}

$GN_MODULE = isset($GLOBALS['routingInfo']['module']) ? current($GLOBALS['routingInfo']['module']) : 'default';

$is_default = TRUE;
foreach ($DB as $module => $DBINFO) {

    $module = strtolower(trim($module));
    $GN_MODULE = strtolower(trim($GN_MODULE));

    if ($module === $GN_MODULE) {
        $GLOBALS['DB'] = $DBINFO;
        $is_default = FALSE;
    }
}
if ($is_default) {
    $GLOBALS['DB'] = isset($DB['default']) ? $DB['default'] : '';
}

#set value for multiple DB use
$GLOBALS['DB_DRIVERS'] = $DB;

class gmodel {

    public $db;

    function __construct() {
        $this->db = (object) array();

        if (isset($GLOBALS['DB_DRIVERS']['HOSTNAME']) && !is_array($GLOBALS['DB_DRIVERS']['HOSTNAME'])) {
            if (file_exists(SYSTEMPATH . 'drivers/' . $GLOBALS['DB_DRIVERS']['DB_DRIVER'] . '/' . $GLOBALS['DB_DRIVERS']['DB_DRIVER'] . 'models.php')) {
                $GLOBALS['DB'][$GLOBALS['DB_DRIVERS']['DB_DRIVER']] = $GLOBALS['DB_DRIVERS'];
                require_once(SYSTEMPATH . 'drivers/' . $GLOBALS['DB_DRIVERS']['DB_DRIVER'] . '/' . $GLOBALS['DB_DRIVERS']['DB_DRIVER'] . 'models.php');
                $dbmodel = $GLOBALS['DB_DRIVERS']['DB_DRIVER'] . "models";
                $this->db = new $dbmodel;
            }
        } else {
            /* for multiple database connectivity */

            foreach ($GLOBALS['DB_DRIVERS'] as $key => $rows) {
                if (file_exists(SYSTEMPATH . 'drivers/' . $rows['DB_DRIVER'] . '/' . $rows['DB_DRIVER'] . 'models.php')) {
                    $GLOBALS['DB'][$rows['DB_DRIVER']] = $rows;
                    require_once (SYSTEMPATH . 'drivers/' . $rows['DB_DRIVER'] . '/' . $rows['DB_DRIVER'] . 'models.php');
                }
                $dbmodel = $rows['DB_DRIVER'] . "models";
                if (is_numeric($key)) {
                    if ($key == 0) {
                        $this->db = new $dbmodel;
                    }
                    $dbclname = 'db' . $key;
                    @$this->{$dbclname} = new $dbmodel;
                } else {
                    $this->db->{$key} = new $dbmodel;
                }
            }
        }
    }
}
