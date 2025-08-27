<?php

namespace model;

use gmodel;

class common extends gmodel {

    function __construct() {
        parent::__construct();
    }

    public function getuserslog($deviceid) {
        $where = array('deviceid', '=', $deviceid);
        return $this->db->els->table('logusers')->select()->where($where)->orderby('timestamp', 'desc')->execute()->row();
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
        $res = $this->db->els->table('logmsflow')->select(['token','parentid', 'childid', 'iserror', 'isreturn', 'isms', 'msid', 'microservicename', 'usersid', 'ukey', 'executiontime'])->where($where)->orderby('timestamp', 'asc')->execute()->result();
        return $res['data'];
    }

}
