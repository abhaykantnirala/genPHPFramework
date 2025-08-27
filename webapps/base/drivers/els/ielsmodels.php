<?php

/**
 *
 */

interface ielsmodels {
    
    public function index($indexname);
    
    public function execute();

    public function inlinequery($info=array('cmd'=>'', 'data' => array(), 'method'=> 'GET'));

    public function viewquery();

    public function getlastquery();

    public function getallquery();

    public function where($where);

    public function row($return_type = 'object');

    public function result($return_type = 'object');

    public function rowaffected();
}
