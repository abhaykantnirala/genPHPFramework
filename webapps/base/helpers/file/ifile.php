<?php

interface ifile {

    public function createdir($dirpath);

    public function save($path, $data, $append = false);

    public function getcontents($path);

    public function getfile($path);
    
    public function getdirlist($path);

    public function getfilelist($path, $ext = array());
    
    public function getfileextension($filename);
    
    public function getfileinfo($file);
    
}
