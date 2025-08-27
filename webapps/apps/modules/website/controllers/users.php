<?php

/**
 * @users controller
 * */
class users extends gcontroller {

    private $data = array();

    function __construct() {
        parent::__construct();
        $this->data['_title'] = 'Software / Web / App Development Company | Saas Websol';
        $this->data['_meta_decp'] = 'Saas Websol - Software / Web / App Development Company | Saas Websol';
        $this->load->model('users');
    }

    public function index() {
        $this->data['udata'] = $aa = $this->session->getdata('user-session-data');
        $this->data['user_emi_data'] = $this->model->users->get_user_emi($this->data['udata']->id);
        $this->data['policy_details'] = $this->load->view('users/get-user-emi', $this->data, true);
        
        $this->load->layout->website('index', $this->data);
    }
    
    public function password_update() {
        $password_length = 6;
        $user_id = $this->session->getdata('user-session-data')->id;
        #get data
        $data = $this->helper->input->post();
        $u_old_password = trim(isset($data['u_old_password']) ? $data['u_old_password'] : '');
        $u_new_password = trim(isset($data['u_new_password']) ? $data['u_new_password'] : '');
        
        if(strlen($u_new_password)<$password_length && $user_id>0){
            echo json_encode([
                'status' => 'failed',
                'msg' => 'Please keep password length atleast '.$password_length
            ]);
            exit(0);
        }
        
        $rowaffected = $this->model->users->password_update($u_old_password, $u_new_password, $user_id);
        if ($rowaffected) {
            echo json_encode([
                'status' => 'success',
                'msg' => 'Password successfully updated.'
            ]);
            exit(0);
        }
        #incase failed
        echo json_encode([
            'status' => 'failed',
            'msg' => 'Password not updated. Please try again.'
        ]);
    }
    
    public function logout(){
        #clear session
        $this->session->setdata('user-session-data', '');
        #redirect to login page
        $this->helper->url->redirect('home');
    }
    
    public function login(){
        #get users post data
        $data = $this->helper->input->post();
        $udata = $this->model->users->user_login($data);
        if (isset($udata->id)){
            $res = array(
                'status' => 'success',
                'code' => 200,
                'msg' => 'Login successfully'
            );
            #set session data here
            $this->session->setdata('user-session-data', $udata);
        } else {
            $res = array(
                'status' => 'fail',
                'code' => 401,
                'msg' => 'Invalid credentials'
            );
        }
        echo json_encode($res);
    }
}