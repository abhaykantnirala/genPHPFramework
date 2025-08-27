<?php

interface igurl {

    public function currenturl();

    public function baseurl($file_path = '');

    public function params();

    public function documentroot($file_path = '');

    public function redirect($path = '');

    public function redirectback();

    public function iscurrenturl($url_path = '');

    public function currentroot($filepath = '');

    public function server();

    public function useragent();
}
