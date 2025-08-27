<?php

/**
 * 
 */
class test extends gcontroller {

    function __construct() {
        parent::__construct();
        $this->load->library('gemt');
    }

    function emtgetbooking() {
        $this->library->gemt->getbookingstatus();
    }

}
