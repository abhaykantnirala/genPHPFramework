<?php

namespace model;

use gmodel;

class website extends gmodel {

    function __construct() {
        parent::__construct();
    }

    public function plans_list() {
        $where = array(
//            array('statusa', '=', 1),
//            'and',
            array('statusb', '=', 1)
        );
        return $this->db->table('plans')->select()->where($where)->orderby('priority', 'asc')->execute()->result();
    }

    public function msflowdata($deviceid) {
        $where = array('deviceid', '=', $deviceid);
        $res = $this->db->els->table('logmsflow')->select('token')->where($where)->orderby('timestamp', 'desc')->execute()->row();
        #check if token field exists
        if (!isset($res['data']->token)) {
            return false;
        }

        #everyting is okay now get data based on token
        $token = $res['data']->token;
        $where = array('token', '=', $token);
        $res = $this->db->els->table('logmsflow')->select(['token', 'parentid', 'childid', 'iserror', 'isreturn', 'isms', 'msid', 'microservicename', 'usersid', 'ukey', 'executiontime'])->where($where)->orderby('timestamp', 'asc')->execute()->result();
        return $res['data'];
    }
}
