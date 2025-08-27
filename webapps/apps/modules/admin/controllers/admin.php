<?php

/**
 * @website controller
 * */
class admin extends gcontroller {

    private $data = array();

    function __construct() {
        parent::__construct();
        $this->load->model('admin');
        $this->data['_title'] = 'Software / Web / App Development Company | Saas Websol';
        $this->data['_meta_decp'] = 'Saas Websol - Software / Web / App Development Company | Saas Websol';

    }

    public function index() {
        $this->data['planwise_info'] = $this->model->admin->dashboard_get_planwise_info();
        $this->data['top_users_info'] = $this->model->admin->get_top_and_total_users_info(15);
        $this->data['recent_users_info'] = $this->model->admin->get_recent_users(5);
        $this->load->layout->admin('index', $this->data);
    }

    public function logout() {
        #clear session
        $this->session->setdata('udata', '');
        #redirect to login page
        $this->helper->url->redirect('admin-signin');
    }

    public function login() {
        #get post request
        $email = $this->helper->input->post('aemail');
        $pwd = $this->helper->input->post('apwd');
        $udata = $this->model->admin->check_login($email, $pwd);
        
        if ($udata) {
            $this->session->setdata('udata', $udata);
            $this->helper->url->redirect('admin-dashboard');
        }
        $this->helper->url->redirect('admin-signin');
    }
    
    public function sign_in(){
        if(!empty($this->session->getdata('udata'))){
            $this->helper->url->redirect('admin-dashboard');
        }
        #else show login page
        $this->load->view('admin/signin');
    }
}
