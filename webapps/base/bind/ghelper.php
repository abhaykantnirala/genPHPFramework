<?php

/**
 * 
 */
class GHelper extends gloader {

	public $load;

    function __construct() {
        parent::__construct();
        $this->load = $this;
        @$this->load->layout = $this;
    }

}
