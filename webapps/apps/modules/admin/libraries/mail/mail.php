<?php

namespace library;

use gcontroller;

class mail extends gcontroller {

    function __construct() {
        parent::__construct();
    }

    public function sendmail($maildata, $html = false) {
        #toEmail
        $to = ($maildata['to']) ?? '';
        #fromEmail
        $from = $maildata['from'] ?? '';
        #ccEmail
        $cc = $maildata['cc'] ?? '';
        #Subject
        $subject = $maildata['subject'] ?? '';
        #the message
        $msg = $maildata['message'] ?? '';
        # use wordwrap() if lines are longer than 70 characters
        #$msg = wordwrap($msg, 70);
        #headers
        // To send HTML mail, the Content-type header must be set
        $headers = 'MIME-Version: 1.0' . "\r\n";
        $headers .= 'Content-type: text/html; charset=UTF-8' . "\r\n";
        #send email
        if ($html) {
            $mailres = mail($to, $subject, $msg, $headers);
        } else {
            $mailres = mail($to, $subject, $msg);
        }
        return $mailres;
    }

}
