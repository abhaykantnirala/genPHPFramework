<?php

namespace library\gemt;

use gcontroller;

class gemt extends gcontroller {

    function __construct() {
        parent::__construct();
    }

    public function getbookingstatus() {
        $this->load->library('gemt/pendingpnr', 'pendingpnr');
        $res = $this->library->pendingpnr->getbookingstatus();
    }

    public function farechek($data) {
        $this->load->library('gemt/gfarecheck');
        if (in_array(APPSERVER, array('live', 'beta', 'alpha', 'localhost'))) {
            return $res = array(
                'farechanged' => false,
                'changesymbol' => '',
                'amount' => 0
            );
        }

        $res = $this->library->gfarecheck->farechek($data);
        if (!isset($res['farechanged']) || !isset($res['changesymbol']) || !isset($res['amount'])) {
            $res = array(
                'farechanged' => false,
                'changesymbol' => '',
                'amount' => 0
            );
        }
        return $res;
    }

}
