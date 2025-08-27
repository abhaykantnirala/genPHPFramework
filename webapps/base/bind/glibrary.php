<?php

/**
 * 
 */
class GLibrary extends gloader {

    protected $load;

    function __construct() {
        parent::__construct();
        $this->load = $this;
        @$this->load->layout = $this;
    }

}
