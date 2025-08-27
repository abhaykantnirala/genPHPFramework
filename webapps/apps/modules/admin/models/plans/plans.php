<?php

namespace model;

use gmodel;

class plans extends gmodel {

    function __construct() {
        parent::__construct();
    }

    public function get_plans_list($pid = 0) {
        #get plan data
        if ($pid) {
            $query = "SELECT * FROM plans WHERE id = " . $pid;
            return $this->db->inlinequery($query)->execute()->row();
        }
        $query = "SELECT * FROM plans ORDER BY priority ASC";
        return $this->db->inlinequery($query)->execute()->result();
    }

    public function edit_plan($data) {
        $plan_type = $this->get_plan_type($data);
        $udata = array(
            'plan_duration' => isset($data['plan_duration']) ? $data['plan_duration'] : '12',
            'plan_name' => isset($data['plan_name']) ? $data['plan_name'] : '',
            'description' => isset($data['plan_desc']) ? $data['plan_desc'] : '',
            'plan_emi_type' => $plan_type,
            'plan_amount' => isset($data['plan_amount']) ? $data['plan_amount'] : '1000',
            'plan_emi' => isset($data['plan_emi']) ? $data['plan_emi'] : '500',
            'priority' => (int) (isset($data['priority']) ? $data['priority'] : 0),
            'statusa' => isset($data['status']) ? $data['status'] : 0,
            'statusb' => isset($data['availability']) ? $data['availability'] : 0
        );
        $where = array('id', '=', $data['plan_id']);
        return $this->db->table('plans')->update($udata)->where($where)->execute()->rowaffected();
    }

    public function create_plan($data) {
        $plan_type = $this->get_plan_type($data);
        $udata = array(
            'plan_duration' => isset($data['plan_duration']) ? $data['plan_duration'] : '12',
            'plan_name' => isset($data['plan_name']) ? $data['plan_name'] : '',
            'description' => isset($data['plan_desc']) ? $data['plan_desc'] : '',
            'plan_emi_type' => $plan_type,
            'plan_amount' => isset($data['plan_amount']) ? $data['plan_amount'] : '1000',
            'plan_emi' => isset($data['plan_emi']) ? $data['plan_emi'] : '500',
            'priority' => (int) (isset($data['priority']) ? $data['priority'] : 1000),
            'statusa' => isset($data['status']) ? $data['status'] : 0,
            'statusb' => isset($data['availability']) ? $data['availability'] : 0
        );
        return $this->db->table('plans')->insert($udata)->execute()->getlastinsertid();
    }

    private function get_plan_type($data) {
        $plan_type = 'monthly';
        if (isset($data['plan_emi_type'])) {
            $plan_type = $data['plan_emi_type'];
        }
        return $plan_type;
    }

    public function get_uid_of_referral_code($rf_code) {
        #get uid if $data['ref_code'] exists
        $where = array('rf_code', '=', $rf_code);
        $row = $this->db->table('users_referral_code')->select()->where($where)->execute()->row();
        if (isset($row->uid)) {
            return $row->uid;
        }
        return 0;
    }

    public function check_referral_code_if_exists($referral_code) {
        $where = array('rf_code', '=', $referral_code);
        $row = $this->db->table('users_referral_code')->select()->where($where)->execute()->row();
        if (isset($row->id)) {
            #return true needs to create new referral code
            return true;
        }
        #return false current referral code can be use
        return false;
    }

    public function insert_referral_code($referral_code, $last_insert_id, $uid) {
        #first check if current users holds referral code if yes then stop
        $where = array('uid', '=', $last_insert_id);
        $row = $this->db->table('users_referral_code')->select()->where($where)->execute()->row();
        if (isset($row->id)) {
            #user has already referral code so return false
            return false;
        }
        $ref_code_data = array(
            'uid' => $last_insert_id,
            'rf_code' => $referral_code,
            'refer_by' => $uid
        );
        return $this->db->table('users_referral_code')->insert($ref_code_data)->execute()->getlastinsertid();
    }
}
