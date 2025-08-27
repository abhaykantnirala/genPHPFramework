<?php

/**
 * 
 */
class GMiddleware extends GLoader {

    public $config;
    public $allitem;
    private $item;
    public $load;

    function __construct() {
        parent::__construct();
        $this->load = $this;
        $this->config = $this->config();
        $this->config->allitem = $this->allitem();
    }

    public function item($key = '') {
        return isset($this->item[$key]) ? $this->item[$key] : '';
    }

    private function config() {
        $this->item = $GLOBALS['g-configs'];
        return $this;
    }

    public function allitem() {
        return $this->item;
    }
}
