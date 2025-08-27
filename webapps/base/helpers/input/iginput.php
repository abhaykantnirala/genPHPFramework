<?php

interface iginput {

    public function get($key = '');

    public function post($key = '');

    public function put();

    public function file($key = '');
    
    public function header($key = '');
}
