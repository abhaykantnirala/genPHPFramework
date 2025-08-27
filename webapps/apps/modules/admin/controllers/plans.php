<?php

/**
 * @website controller
 * */
class plans extends gcontroller {

    private $data = array();

    function __construct() {
        parent::__construct();
        #load users model
        $this->load->model('plans');
        $this->load->model('users');
        $this->data['_title'] = 'Software / Web / App Development Company | Saas Websol';
        $this->data['_meta_decp'] = 'Saas Websol - Software / Web / App Development Company | Saas Websol';
    }

    public function index() {
        $this->data['plans_list'] = $this->model->plans->get_plans_list();
        $this->load->layout->admin('plans/plans-list', $this->data);
    }

    public function add_plan() {
        $this->data["action"] = "create";
        $this->load->layout->admin('plans/plans-create', $this->data);
    }

    public function update_plan() {
        $pid = $this->helper->input->get('pid');
        $this->data["action"] = "update";
        $this->data['plan_data'] = $this->model->plans->get_plans_list($pid);
        $this->load->layout->admin('plans/plans-create', $this->data);
    }

    public function edit_plan() {
        #get plans post data
        $data = $this->helper->input->post();
        $plan_pic = $this->helper->input->file('plans-picture');
        $pic_upload_flag = false;
        
        if (isset($plan_pic['error']) && $plan_pic['error']==0){
            $pic_upload_flag = true;
        }
        
        if ($pic_upload_flag && isset($plan_pic['type']) && !in_array($plan_pic['type'], array('image/png', 'image/PNG', 'image/JPG', 'image/jpg', 'image/jpeg', 'image/JPEG'))) {
            echo 'Invalid picture';
            die;
        }
        $rowaffected = false;
        if ($this->validate_plans_data($data)) {
            #do registration using model
            $rowaffected = $this->model->plans->edit_plan($data);
        }
        if ($rowaffected) {
            if ($pic_upload_flag) {
                $this->load->helper('ucommon');
                #now upload user pic
                $tmp_name = $plan_pic['tmp_name'];
                $destination = 'public/images/plans-pic';
                $file_type = 'png';
                $file_name = $data['plan_id'];
                $res = $this->helper->ucommon->upload_file($tmp_name, $destination, $file_type, $file_name);
            }
            #redirect to user list page
            $this->helper->url->redirect('plans-list');
        }
        #redirect back
        $this->helper->url->redirectback();
    }

    public function create_plan() {
        #get plans post data
        $data = $this->helper->input->post();
        $plan_pic = $this->helper->input->file('plans-picture');
        if (isset($plan_pic['type']) && !in_array($plan_pic['type'], array('image/png', 'image/PNG', 'image/JPG', 'image/jpg', 'image/jpeg', 'image/JPEG'))) {
            echo 'Invalid picture';
            die;
        }
        $rowaffected = false;
        if ($this->validate_plans_data($data)) {
            #do registration using model
            $last_insert_id = $this->model->plans->create_plan($data);
            #generate referral code for users
            if ($last_insert_id) {
                $rowaffected = true;
            } else {
                $rowaffected = false;
            }
        }
        if ($rowaffected) {
            if (isset($plan_pic['tmp_name'])) {
                $this->load->helper('ucommon');
                #now upload user pic
                $tmp_name = $plan_pic['tmp_name'];
                $destination = 'public/images/plans-pic';
                $file_type = 'png';
                $file_name = $last_insert_id;
                $res = $this->helper->ucommon->upload_file($tmp_name, $destination, $file_type, $file_name);
            }
            #redirect to user list page
            $this->helper->url->redirect('plans-list');
        }
        #redirect back
        $this->helper->url->redirectback();
    }

    private function validate_plans_data($data) {
        return true;
    }
}
