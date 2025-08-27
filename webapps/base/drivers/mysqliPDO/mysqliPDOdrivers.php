<?php

/**
 * 
 */

if (file_exists(SYSTEMPATH . 'drivers/' . $GLOBALS['DB']['mysqliPDO']['DB_DRIVER'] . '/mysqliPDOconnection.php')) {
    require_once(SYSTEMPATH . 'drivers/' . $GLOBALS['DB']['mysqliPDO']['DB_DRIVER'] . '/mysqliPDOconnection.php');
}

class mysqliPDOdrivers extends mysqliPDOconnection {

    protected $_dquery;

    function __construct() {
        parent::__construct();
    }

    protected function _query($mysql_command, $action = FALSE) {
        $this->_dquery = $this->CONNECTION->prepare($mysql_command);
        try {
            $this->_dquery->execute();
        } catch (PDOException $e) {
            if ($this->DB_DEBUG === TRUE || $this->DB_DEBUG === true) {
                echo '<h1>Database Error</h1>';
                echo '<h4><span style="color:red;">Error: </span>' . $e->getMessage() . '</h4>';
                echo '<h4><span style="color:blue;">Query: </span>' . $mysql_command . '</h4>';
                exit(3);
            }
        }
    }

    protected function _get_insert_id() {
        return $this->CONNECTION->lastInsertId();
    }

    protected function _rowaffected() {
        $this->_dquery->rowCount();
    }

    protected function _get_assoc_data($query = 0) {
        return $query == 1 ? $this->_dquery->fetchAll(5) : $this->_dquery->fetch(5);
    }

    protected function _get_object_data($query = 0) {
        return $query == 1 ? $this->_dquery->fetchAll(2) : $this->_dquery->fetch(2);
    }

}
