<?php

namespace model;

use gmodel;

class els extends gmodel {

    function __construct() {
        parent::__construct();
    }

    public function _insert($table, $data) {
        return $this->db->els->table($table)->insert($data)->execute()->getlastinsertid();
    }

    public function _update($table, $data, $where) {
        return $this->db->els->table($table)->update($data)->where($where)->execute()->rowaffected();
    }

    public function _delete($table, $where) {
        return $this->db->els->table($table)->delete()->where($where)->execute();
    }

    public function _result($table, $where = array()) {
        return $this->db->els->table($table)->select()->where($where)->execute()->result();
    }

    public function _row($table, $where = array()) {
        return $this->db->els->table($table)->select()->where($where)->execute()->row();
    }

    public function _insertnowait($table, $data) {
        return $this->db->els->table($table)->insert($data)->execute()->getlastinsertid();
    }

    public function _updatenowait($table, $data, $where) {
        return $this->db->els->table($table)->update($data)->where($where)->execute()->rowaffected();
    }

    public function _deletenowait($table, $where) {
        return $this->db->els->table($table)->delete()->where($where)->execute();
    }

    public function _checktoken($token) {
        $where = array('token', '=', $token);
        $row = $this->_row(_ELSPREFIX_ . 'tokens', $where);
        $totalrecord = $row['totalrecord']->value ?? 0;
        if ($totalrecord == 1) {
            $row = $row['data'];
        }

        if (!empty($row->token)) {
            return $row;
        } else {
            return false;
        }
    }

}
