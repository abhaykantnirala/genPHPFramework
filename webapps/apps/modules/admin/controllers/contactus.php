<?php

/**
 * @website controller
 * */
class contactus extends gcontroller {

    private $data = array();

    function __construct() {
        parent::__construct();
        #load users model
        //$this->load->model('contactus');
        $this->data['_title'] = 'Contact Us';
        $this->data['_meta_decp'] = 'Saas Websol - Software / Web / App Development Company | Saas Websol';
    }

    public function customer_contact_request() {
        //$this->data['users_list'] = $this->model->users->get_users_list();
        $this->load->layout->admin('contactus/contactus-customer-request', $this->data);
    }
    
    public function get_customer_contact_request(){
        #example from https://datatables.net/examples/data_sources/server_side
        $data = $this->helper->input->get();
        $draw = $data['draw'];
        $start = $data['start'];
        $length = $data['length'];
//        echo '<pre>';
//        print_r($data);
//        die;
        echo '{"draw":'.$draw.',"recordsTotal":157,"recordsFiltered":157,"data":[["Tiger","Nixon","System Architect","Edinburgh","25th Apr 11","$320,800"],["Timothy","Mooney","Office Manager","London","11th Dec 08","$136,200"],["Unity","Butler","Marketing Designer","San Francisco","9th Dec 09","$85,675"],["Vivian","Harrell","Financial Controller","San Francisco","14th Feb 09","$452,500"],["Yuri","Berry","Chief Marketing Officer (CMO)","New York","25th Jun 09","$675,000"],["Zenaida","Frank","Software Engineer","New York","4th Jan 10","$125,250"],["Zorita","Serrano","Software Engineer","San Francisco","1st Jun 12","$115,000"],["Zenaida","Frank","Software Engineer","New York","4th Jan 10","$125,250"],["Zorita","Serrano","Software Engineer","San Francisco","1st Jun 12","$115,000"],["Zenaida","Frank","Software Engineer","New York","4th Jan 10","$125,250"],["Zorita","Serrano","Software Engineer","San Francisco","1st Jun 12","$115,000"]]}';
    }


    public function view_emi(){
        $this->data['users_list'] = $this->model->users->get_users_list();
        $this->load->layout->admin('users/view-user-emi', $this->data);
    }

    public function plan_allotment($uid) {
        $user_data = $this->model->users->get_user_data($uid);
        $user_plans = $this->model->users->get_user_plans_list($uid);
        $plans_list = $this->model->users->get_plans_list();
        #remove taken plan by user
        foreach($plans_list as $key=>$row){
            foreach($user_plans as $plans){
                if($plans->id==$row->id){
                    unset($plans_list[$key]);
                }
            }
        }
        
        $this->data['user_data'] = $user_data;
        $this->data['user_plans'] = $user_plans;
        $this->data['plans_list'] = $plans_list;
        $this->load->layout->admin('users/users-plan-allotment', $this->data);
    }
    
    public function emi_updation_form($uid){
        $this->data['user_emi_data'] = $this->model->users->get_user_emi($uid);
        $this->data['user_emi_data'] = $this->load->view('users/get-user-emi', $this->data, true);
 
        $this->data['user_id'] = $uid;
        $this->data['user_data'] = $this->model->users->get_user_data($uid);
        $this->data['plans_list'] = $this->model->users->get_user_plans_list($uid);
        $this->load->layout->admin('users/users-emi-update', $this->data);
    }
    
    public function user_emi_update(){
        #get data
        $data = $this->helper->input->post();
        $user_id = isset($data['user_id']) ? $data['user_id'] : 0;
        $user_plans_id = isset($data['user_plans_id']) ? $data['user_plans_id'] : 0;
        $emi_amount = isset($data['emi_amount']) ? $data['emi_amount'] : 0;
        $payment_method = isset($data['payment_method']) ? $data['payment_method'] : "";
        $comment = isset($data['comment']) ? $data['comment'] : "No comment";
        $late_fine = isset($data['late_fine']) ? $data['late_fine'] : 0;
        $emi_date = isset($data['emi_date']) ? $data['emi_date'] : date('Y-m-d');
          
        $last_insert_id = $this->model->users->update_user_received_emi($user_id, $user_plans_id, $emi_amount, $payment_method, $late_fine, $emi_date, $comment);
        if ($last_insert_id) {
            #redirect to user list page
            $this->helper->url->redirect('users-list');
        }
        #redirect back
        $this->helper->url->redirectback();
    }
    
    public function user_emi_record($uid) {
        $this->data['user_id'] = $uid;
        $this->data['user_data'] = $this->model->users->get_user_data($uid);
        $this->data['plans_list'] = $this->model->users->get_emi_record($uid);
        $this->load->layout->admin('users/users-emi-update', $this->data);
    }

    public function allot_plan() {
        #get data
        $data = $this->helper->input->post();
        $user_id = isset($data['user_id']) ? $data['user_id'] : 0;
        $plan_id = isset($data['plan_id']) ? $data['plan_id'] : 0;
        $last_insert_id = $this->model->users->create_user_plan($user_id, $plan_id);
        if ($last_insert_id) {
            #redirect to user list page
            $this->helper->url->redirect('users-list');
        }
        #redirect back
        $this->helper->url->redirectback();
    }
    
    public function registration() {
        $this->load->layout->admin('users/users-registration');
    }

    public function do_registration() {
        #get users post data
        $data = $this->helper->input->post();
        $user_pic = $this->helper->input->file('user-picture');
        if (isset($user_pic['type']) && !in_array($user_pic['type'], array('image/png', 'image/PNG', 'image/JPG', 'image/jpg', 'image/jpeg', 'image/JPEG'))) {
            echo 'Invalid picture';
            die;
        }


        $rowaffected = false;
        if (!$this->validate_users_data($data)) {
            #do registration using model
            $last_insert_id = $this->model->users->do_registration($data);
            #generate referral code for users
            if ($last_insert_id) {
                $rowaffected = true;
                $rf_code = isset($data['rf_code']) ? $data['rf_code'] : '';
                $uid = $this->model->users->get_uid_of_referral_code($rf_code);
                #create referral code
                $referral_code_created = $this->create_referral_code($last_insert_id, $uid);
            } else {
                $rowaffected = false;
            }
        }
        if ($rowaffected) {
            if (isset($user_pic['tmp_name'])) {
                $this->load->helper('ucommon');
                #now upload user pic
                $tmp_name = $user_pic['tmp_name'];
                $destination = 'public/images/users-pic';
                $file_type = 'png';
                $file_name = $last_insert_id;
                $res = $this->helper->ucommon->upload_file($tmp_name, $destination, $file_type, $file_name);
            }
            #redirect to user list page
            $this->helper->url->redirect('users-list');
        }
        #redirect back
        $this->helper->url->redirectback();
    }

    private function validate_users_data($data) {
        return $this->model->users->validate_users($data);
    }

    private function create_referral_code($last_insert_id, $uid = 0) {
        #load helper
        $this->load->helper('ucommon');
        #check referral code if not exists then insert or regenerate new
        do {
            $referral_code = $this->helper->ucommon->generate_referral_code($last_insert_id);
        } while ($this->model->users->check_referral_code_if_exists($referral_code));
        #now insert referral code
        if ($referral_code) {
            return $this->model->users->insert_referral_code($referral_code, $last_insert_id, $uid);
        }
        return false;
    }
}
