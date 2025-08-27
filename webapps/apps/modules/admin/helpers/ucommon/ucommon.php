<?php

namespace helper;

use gcontroller;

class ucommon extends gcontroller {

    function __construct() {
        parent::__construct();
    }

    public function generate_referral_code($seed, $extralength = 4) {
        #create a Alphabet Numeric string
        $heystack = str_split('QWERTYUIOPASDFGHJKLZXCVBNM0123456789');
        $referral_code = array();
        #add seeds
        $referral_code[] = $seed;
        for ($i = 0; $i < $extralength; $i++) {
            $p = rand(0, count($heystack) - 1);
            #add new char
            $referral_code[] = $heystack[$p];
        }
        #convert to string
        $referral_code = implode('', $referral_code);
        #return referral code generated
        return $referral_code;
    }

    public function upload_file($tmp_name, $destination, $filetype, $file_name) {
        $path = implode("/", array($destination, $file_name . '.' . $filetype));
        return copy($tmp_name, $path);
    }
}
