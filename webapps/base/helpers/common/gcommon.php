<?php

namespace base\helper;

class common {

    public $offsetvalue = 19800; #Asia/Kolkata
    public $currenttimezone = 'Asia/Kolkata';

    function __construct() {
        $this->offsetvalue = 19800; #Asia/Kolkata
        $this->currenttimezone = date_default_timezone_get();
    }
    
    public function currency_format($amount, $currency='INR'){
        $formatter = new \NumberFormatter('en_IN', \NumberFormatter::CURRENCY);
	    return $formatter->formatCurrency($amount, $currency);
    }

    public function gettime($minute) {
        $du = str_pad(floor($minute / 60), 2, 0, STR_PAD_LEFT) . ':' . str_pad(($minute % 60), 2, 0, STR_PAD_RIGHT);
        return $du;
    }

    public function gethmduration($durationinminute) {
        if ($durationinminute > 59) {
            $duration = (floor($durationinminute / 60)) . ' hr ' . ($durationinminute % 60) . ' min';
        } else {
            $duration = $durationinminute . ' min';
        }
        return $duration;
    }

    public function getdefaulttimezone() {
        return $this->currenttimezone;
    }

    public function settimezonebyoffsetvalue($offesetvalue = false) {
        if ($offesetvalue) {
            $this->offsetvalue = $offesetvalue;
        }
        $timezone_name = timezone_name_from_abbr('', $this->offsetvalue, FALSE);
        #$timezone_name = 'Europe/Berlin';
        date_default_timezone_set($timezone_name);
    }

    public function gettimezonenamebyoffsetvalue($offesetvalue = false) {
        if ($offesetvalue) {
            $this->offsetvalue = 0;
        }
        $timezone_name = timezone_name_from_abbr('', $this->offsetvalue, FALSE);
        return $timezone_name;
    }

    public function resettimezone() {
        date_default_timezone_set($this->currenttimezone);
    }

}
