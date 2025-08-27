<?php

/**
 * 
 */
require_once(SYSTEMPATH . 'bind/gloader.php');

class loader extends gloader {

    protected $load;

    function __construct() {
        parent::__construct();
        $this->load->layout = $this->load = $this;
    }

}
