<?php
/*
* Nexo Encryption/Decryption class
*/

class aescryption {

    private $iv = 'A148AF899A776ADC';
    private $keyenc = "CE618E1FACD6AEAD";

    public function encrypt($str, $isbinary = true) {
        //add the utf8_decode encoding to key
        $encryption_key = utf8_decode($this->keyenc);
        //Encrypt the data using AES 128 encryption in CBC mode using our encryption key and initialization vector.
        $encrypted = openssl_encrypt($str, 'aes-128-cbc', $encryption_key, OPENSSL_RAW_DATA, $this->iv);
        //get result based on isbinary
        $result = $isbinary ? $encrypted : bin2hex($encrypted);
        return $result;
    }

    public function decrypt($code, $isbinary = true) {
        //remove bin2hex encoding
        $code = $isbinary ? $code : @hex2bin($code);
        //add the utf8_decode encoding to key
        $encryption_key = utf8_decode($this->keyenc);
        //Decrypt the data using AES 128 encryption in CBC mode using our encryption key and initialization vector.
        $decrypted = openssl_decrypt($code, 'aes-128-cbc', $encryption_key, OPENSSL_RAW_DATA|OPENSSL_ZERO_PADDING, $this->iv);
        //get result based on isbinary
        $result = $isbinary ? $decrypted : utf8_encode($decrypted);
        //$result = preg_replace("/[^ \w]+/", "", $result);
        return $result;
    }
}