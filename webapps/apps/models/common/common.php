<?php

namespace model;

use gmodel;

class common extends gmodel {

    function __construct() {
        parent::__construct();
    }

    public function _insert($table, $data) {
        return $this->db->table($table)->insert($data)->execute()->getlastinsertid();
    }

    public function _update($table, $data, $where) {
        return $this->db->table($table)->update($data)->where($where)->execute();
    }

    public function _delete($table, $where) {
        return $this->db->table($table)->delete()->where($where)->execute();
    }

    public function _row($sql) {
        return $this->db->inlinequery($sql)->execute()->row();
    }

    public function _result($sql) {
        return $this->db->inlinequery($sql)->execute()->result();
    }

    public function _data($sql) {
        return $this->db->inlinequery($sql)->execute()->result('array');
    }

    public function _cominsert($table, $data) {
        return $this->db->com->table($table)->insert($data)->execute()->getlastinsertid();
    }

    public function _comupdate($table, $data, $where) {
        return $this->db->com->table($table)->update($data)->where($where)->execute();
    }

    public function _comdelete($table, $where) {
        return $this->db->com->table($table)->delete()->where($where)->execute();
    }

    public function _comrow($sql) {
        return $this->db->com->inlinequery($sql)->execute()->row();
    }

    public function _comresult($sql) {
        return $this->db->com->inlinequery($sql)->execute()->result();
    }

    public function _comdata($sql) {
        return $this->db->com->inlinequery($sql)->execute()->result('array');
    }

}
