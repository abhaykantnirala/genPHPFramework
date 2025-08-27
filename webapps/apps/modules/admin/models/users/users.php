<?php

namespace model;

use gmodel;

class users extends gmodel {

    function __construct() {
        parent::__construct();
    }
    
    public function get_user_emi($uid){
        $query = "SELECT plans.id as plan_id, plans.plan_name, plans.plan_duration, plans.plan_amount, plans.plan_emi_type, user_plan_emi.emi_amount, user_plan_emi.emi_received_method, user_plan_emi.datetime as emi_date FROM user_plan_emi JOIN users_plans ON users_plans.id = user_plan_emi.user_plans_id JOIN plans ON users_plans.plan_id = plans.id WHERE user_plan_emi.user_id = ".$uid." ORDER BY user_plan_emi.datetime ASC";

        $res = $this->db->inlinequery($query)->execute()->result();
        #now create seperate array for each plan
        $emi_data = array();
        foreach($res as $row){
            if (!isset($emi_data[$row->plan_name])){
                $emi_data[$row->plan_name] = array();
            }
            $emi_data[$row->plan_name][] = $row; 
        }
        return $emi_data;
    }

    public function get_users_list() {
        #get users list
        #$query = "SELECT users.id as uid, users.tmppwd as tmppwd, users.fname as fname, users.lname as lname, users.phone as phone, users.email as email, users.city as city, users.state as state, users.address_1 as address_1, users.address_2 as address_2, date(users.datetime) as datetime, users_referral_code.rf_code as rf_code FROM users JOIN users_referral_code ON users.id=users_referral_code.uid";

        $query = "SELECT users.id as uid, users.tmppwd as tmppwd, users.fname as fname, users.lname as lname, users.phone as phone, users.email as email, users.city as city, users.state as state, users.address_1 as address_1, users.address_2 as address_2, date(users.datetime) as datetime, users_referral_code.rf_code as rf_code FROM users JOIN users_referral_code ON users.id=users_referral_code.uid JOIN users_plans ON users_plans.user_id=users.id AND users_plans.statusa=0;";
        
        return $this->db->inlinequery($query)->execute()->result();
    }

    public function get_user_data($uid) {
        #get user data
        $query = "SELECT users.id as uid, users.tmppwd as tmppwd, users.fname as fname, users.lname as lname, users.phone as phone, users.email as email, users.city as city, users.state as state, users.address_1 as address_1, users.address_2 as address_2, date(users.datetime) as datetime, users_referral_code.rf_code as rf_code FROM users JOIN users_referral_code ON users.id=" . $uid . " and users.id=users_referral_code.uid";
        return $this->db->inlinequery($query)->execute()->row();
    }

    public function get_user_plans_list($uid) {
        $query = "SELECT users_plans.id as user_plans_id, plans.plan_emi, users_plans.user_id as uid, plans.id, plans.plan_name, plans.plan_duration, plans.plan_emi_type, plans.plan_amount, plans.datetime, users_plans.total_emi_received FROM users_plans JOIN plans ON users_plans.plan_id=plans.id WHERE users_plans.statusa=0 and users_plans.statusb=0 and users_plans.user_id = " . $uid;
        return $this->db->inlinequery($query)->execute()->result();
    }

    public function update_user_received_emi($user_id, $user_plans_id, $emi_amount, $payment_method, $late_fine, $emi_date, $comment) {
        $udata = array(
            'user_id' => $user_id,
            'user_plans_id' => $user_plans_id,
            'emi_amount' => $emi_amount,
            'late_fine' => $late_fine,
            'emi_received_method' => $payment_method,
            'datetime' => $emi_date,
            'comment' => $comment
        );
        $last_insert_id = $this->db->table('user_plan_emi')->insert($udata)->execute()->getlastinsertid();
        if ($last_insert_id ){
            $query = "UPDATE users_plans SET total_emi_received = total_emi_received + 1 WHERE id = ".$user_plans_id;
            $rowaffected = $this->db->inlinequery($query)->execute()->rowaffected();
            return $last_insert_id;
        }
        return false;
    }
    
    public function get_plans_list() {
        #get user data
        $query = "SELECT *From plans where statusa=1";
        return $this->db->inlinequery($query)->execute()->result();
    }

    public function create_user_plan($user_id, $plan_id) {
        $udata = array(
            'user_id' => $user_id,
            'plan_id' => $plan_id
        );
        return $this->db->table('users_plans')->insert($udata)->execute()->getlastinsertid();
    }

    public function do_registration($data) {
        $password = $this->generate_password();
        $udata = array(
            'password' => md5($password),
            'tmppwd' => $password,
            'fname' => isset($data['fname']) ? $data['fname'] : '',
            'lname' => isset($data['lname']) ? $data['lname'] : '',
            'aadhaar_number' => isset($data['aadhaar_number']) ? $data['aadhaar_number'] : '',
            'pan_number' => isset($data['pan_number']) ? $data['pan_number'] : '',
            'country' => isset($data['country_name']) ? $data['country_name'] : '',
            'state' => isset($data['city']) ? $data['city'] : '',
            'address_1' => isset($data['address1']) ? $data['address1'] : '',
            'address_2' => isset($data['address2']) ? $data['address2'] : '',
            'phone' => isset($data['phone']) ? $data['phone'] : '',
            'zip' => isset($data['zipcode']) ? $data['zipcode'] : '',
        );
        return $this->db->table('users')->insert($udata)->execute()->getlastinsertid();
    }

    public function validate_users($data) {
        $phone = isset($data['phone']) ? $data['phone'] : '';
        $where = array('phone', '=', $phone);
        $row = $this->db->table('users')->select()->where($where)->execute()->row();
        if (isset($row->id)) {
            #return true if user found
            return true;
        }
        #return false if user not found
        return false;
    }

    private function generate_password() {
        return rand(100000, 999999);
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
