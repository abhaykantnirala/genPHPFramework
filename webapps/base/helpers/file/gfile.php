<?php

class file {

    public $params;
    public $base_url;
    public $document_root;
    public $server;

    function __construct() {
        $this->params = $GLOBALS['params'];
    }

    public function createdir($dirpath) {
        #check if directory exists
        if (!is_dir($dirpath)) {
            #try to create directory
            return mkdir($dirpath, 0777, true);
        } else {
            return true;
        }
    }

    public function save($path, $data, $append = false) {
        $cpath = explode('/', str_replace('//', '/', $path));
        $file = end($cpath);
        unset($cpath[count($cpath) - 1]);
        #try to create directory
        $cpath = implode('/', $cpath);

        if (!empty($cpath) && !$this->createdir($cpath)) {
            die('Unable to create directory <h2>' . $cpath . '</h2>');
        }

        #move ahead to create file
        $filename = $path;
        $error = false;
        if ($append) {
            file_put_contents($filename, $data, FILE_APPEND);
        } else {
            file_put_contents($filename, $data);
        }

        if ($error) {
            die($error);
        }

        return true;
    }

    public function getcontents($path) {
        if ($path == "php://input") {
            return @file_get_contents($path);
        } else if (file_exists($path)) {
            return @file_get_contents($path);
        }
        die('File <h2>' . $path . '</h2> not exists');
    }

    public function getfile($path) {
        if (file_exists($path)) {
            return file($path);
        }
        die('File <h2>' . $path . '</h2> not exists');
    }

    public function getdirlist($dir) {
        $list = $this->getdirlisting($dir);
        return $list;
    }

    private function getdirlisting($dir) {
        $root = scandir($dir);
        foreach ($root as $value) {
            if ($value === '.' || $value === '..') {
                continue;
            }

            if (is_file("$dir/$value")) {
                $result[] = str_replace("//", '/', "$dir/$value");
                continue;
            }

            if (is_dir("$dir/$value")) {
                $curdir = $this->getdirlisting(str_replace("//", '/', "$dir/$value"));
                if (is_array($curdir)) {
                    foreach ($curdir as $value) {
                        $result[] = $value;
                    }
                }
            }
        }
        return isset($result) ? $result : false;
    }

    public function getfilelist($path, $ext = array()) {
        $flag = false;
        if (count($ext)) {
            $flag = true;
        }
        $files = array();
        if ($handle = opendir($path)) {
            while (false !== ($file = readdir($handle))) {
                if ($file != "." && $file != "..") {
                    if ($flag) {
                        if (in_array($this->getfileextension($file), $ext)) {
                            $files[$file] = filemtime($path . '/' . $file);
                        }
                    } else {
                        $files[$file] = filemtime($path . '/' . $file);
                    }
                }
            }
            closedir($handle);
        }
        #sort by file update time in descending order
        arsort($files);
        $files = array_keys($files);
        return $files;
    }

    public function getfileinfo($file) {
        if (!file_exists($file)) {
            return false;
        }

        $file_info = array();
        $pathinfo = pathinfo($file);
        $stat = stat($file);
        $file_info['realpath'] = realpath($file);
        $file_info['dirname'] = $pathinfo['dirname'];
        $file_info['basename'] = $pathinfo['basename'];
        $file_info['filename'] = $pathinfo['filename'];
        $file_info['extension'] = $pathinfo['extension'];
        $file_info['mime'] = finfo_file(finfo_open(FILEINFO_MIME_TYPE), $file);
        $file_info['encoding'] = finfo_file(finfo_open(FILEINFO_MIME_ENCODING), $file);
        $file_info['size'] = $stat[7];
        $file_info['size_string'] = $this->format_bytes($stat[7]);
        $file_info['atime'] = $stat[8];
        $file_info['mtime'] = $stat[9];
        $file_info['permission'] = substr(sprintf('%o', fileperms($file)), -4);
        $file_info['fileowner'] = getenv('USERNAME');

        return (object) $file_info;
    }

    /**
     * @param int => $size = valor em bytes a ser formatado
     */
    private function format_bytes(int $size) {
        $base = log($size, 1024);
        $suffixes = array('', 'KB', 'MB', 'GB', 'TB');
        return round(pow(1024, $base - floor($base)), 2) . '' . $suffixes[floor($base)];
    }

    public function getfileextension($filename) {
        $extension = @end(explode(".", $filename));
        return $extension ? $extension : false;
    }
}
