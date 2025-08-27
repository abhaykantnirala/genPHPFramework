<?php

namespace model;

use gmodel;

class users extends gmodel {

    function __construct() {
        parent::__construct();
    }
    
    public function password_update($u_old_password, $u_new_password, $user_id){
        $query = "UPDATE users SET tmppwd='updated by user', password = '".md5($u_new_password)."' WHERE id = ".$user_id. " and password = '".md5($u_old_password)."'";
        return $this->db->inlinequery($query)->execute()->rowaffected();
    }
    
    public function get_user_emi($uid){
        $query = "SELECT plans.id as plan_id, plans.plan_name, plans.plan_duration, plans.plan_amount, plans.plan_emi_type, user_plan_emi.emi_amount, user_plan_emi.emi_received_method, user_plan_emi.datetime as emi_date, users_plans.statusa as emi_completed FROM user_plan_emi JOIN users_plans ON users_plans.id = user_plan_emi.user_plans_id JOIN plans ON users_plans.plan_id = plans.id WHERE user_plan_emi.user_id = ".$uid." ORDER BY user_plan_emi.datetime ASC";
        
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
    
    public function user_login($data){
        $pwd = md5(isset($data['pwd'])?$data['pwd']:'');
        $mobile = isset($data['mobile'])?$data['mobile']:'';
        
        $where = array(
            array('password', '=', $pwd),
            'and',
            array('phone', '=', $mobile)
        );
        return $this->db->table('users')->select()->where($where)->execute()->row();
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
