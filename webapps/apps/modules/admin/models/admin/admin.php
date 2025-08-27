<?php

namespace model;

use gmodel;

class admin extends gmodel {

    function __construct() {
        parent::__construct();
    }

    public function check_login($email, $pwd) {
        $where = array(
            array('email', '=', $email),
            'and',
            array('password', '=', md5($pwd)),
        );
        return $this->db->table('admins')->select()->where($where)->execute()->row();
    }
    
    public function dashboard_get_planwise_info(){
        $query = "SELECT plan_id, plan_name, (SELECT plan_amount FROM plans WHERE id=a.plan_id) AS plan_amount, (SELECT plan_emi FROM plans WHERE id=a.plan_id) AS plan_emi, tot_users, (SELECT SUM(emi_amount) FROM user_plan_emi WHERE user_plans_id IN (SELECT id FROM users_plans WHERE users_plans.plan_id=a.plan_id)) AS total_emi_received FROM 
            (
            	SELECT plan_id, (SELECT plan_name FROM plans WHERE id=plan_id) As plan_name, COUNT(user_id) as tot_users FROM users_plans WHERE statusa=0 AND statusb=0 AND total_emi_received>0  GROUP BY plan_id
            ) AS a";
        return $this->db->inlinequery($query)->execute()->result();
    }
    
    public function get_recent_users($limit=5){
        $query = "SELECT CONCAT(fname, ' ', lname) AS user_name, datetime FROM users ORDER BY id DESC LIMIT ".$limit;
        return $this->db->inlinequery($query)->execute()->result();
    }
    
    public function get_top_and_total_users_info($limit=15){
        #$query = "SELECT user_id, (SELECT COUNT(id) FROM users) AS tot_users, (SELECT CONCAT(fname, ' ', lname) FROM users WHERE id=user_id) AS user_name, income FROM (SELECT user_id, SUM(emi_amount) AS income FROM user_plan_emi GROUP BY user_id) AS a ORDER BY a.income DESC LIMIT ".$limit;
        $query = "SELECT 
                a.user_id, 
                (SELECT COUNT(id) FROM users) AS tot_users, 
                (SELECT CONCAT(fname, ' ', lname) FROM users WHERE id = a.user_id) AS user_name, 
                a.income
            FROM (
                SELECT 
                    user_plan_emi.user_id, 
                    SUM(emi_amount) AS income
                FROM user_plan_emi
                JOIN users_plans 
                    ON users_plans.user_id = user_plan_emi.user_id 
                AND users_plans.statusa = 0
                GROUP BY user_plan_emi.user_id
            ) AS a
            ORDER BY a.income DESC LIMIT ".$limit;
        return $this->db->inlinequery($query)->execute()->result();
    }

}
