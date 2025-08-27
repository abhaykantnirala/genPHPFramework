<?php

/**
 * @website controller
 * */
class website extends gcontroller {

    private $data = array();

    function __construct() {
        parent::__construct();
        $this->data['_title'] = 'Software / Web / App Development Company | Saas Websol';
        $this->data['_meta_decp'] = 'Saas Websol - Software / Web / App Development Company | Saas Websol';
    }

    public function index() {
        $this->load->layout->website('index', $this->data);
    }

    public function about() {
        $this->data['_title'] = 'About us';
        $this->data['_meta_decp'] = 'About us';
        $this->load->layout->website('about', $this->data);
    }

    public function contact() {
        $this->load->layout->website('contact', $this->data);
    }

    public function privacy_policy() {
        $this->load->layout->website('privacy-policy', $this->data);
    }

    public function plansdetail() {
        $this->load->layout->website('plans-detail', $this->data);
    }

    public function terms() {
        $this->load->layout->website('terms');
    }

    public function plans() {
        $this->load->model('website');
        $this->data['plans_list'] = $this->model->website->plans_list();
        $this->load->layout->website('plans', $this->data);
    }

    public function sendmail() {
        $data = $this->helper->input->post();

        if (!is_array($data)) {
            $return = array(
                'status' => 'fail',
                'data' => false
            );
            echo json_encode($return);
            exit(0);
        }
        if (is_array($data) && (empty($data['first_name']) || empty($data['last_name']) || empty($data['email']) || empty($data['phone'] || empty($data['message'])))) {
            $return = array(
                'status' => 'fail',
                'data' => false
            );
            echo json_encode($return);
            exit(0);
        }
//        $data = array(
//            'first_name' => 'Abhaykant',
//            'last_name' => 'Nirala',
//            'email' => 'test@gmail.com',
//            'phone' => '91-8871991972',
//            'message' => 'Hello World to first mail'
//        );
        #HTML mail
        #$msg = $this->load->view('mail/template', $data);

        $msg = array(
            "First Name: " . ucfirst(strtolower($data['first_name'])),
            "\r\n",
            "Last Name: " . ucfirst(strtolower($data['last_name'])),
            "\r\n",
            "Email: " . strtolower($data['email']),
            "\r\n",
            "Phone: " . $data['phone'],
            "\r\n",
            "Message: " . $data['message']
        );

        $msg = implode('', $msg);

        $maildata = array(
            'to' => 'sales@saaswebsol.com',
            'cc' => '',
            'from' => '',
            'subject' => 'Contact Us - User Reqest',
            'message' => $msg
        );

        $this->load->library('mail');
        $res = $this->library->mail->sendmail($maildata);
        $return = array(
            'status' => 'success',
            'data' => $res
        );
        echo json_encode($return);
    }

    public function do_login() {
        $receivedData = $this->helper->input->post();
        $arr = array(
            'status' => 'success',
            'messaage' => 'Loged In successfully',
            'data' => $receivedData
        );
        echo json_encode($arr);
    }
}

// 1. public => css, js


// 2. apps/modules/website/controller =>  

// public function ram() {
//         $this->load->layout->website('faqs');
//     }


// 3. apps/modules/website/view/website =>  

// body pages


// 4. apps/modules/website/layout/website.php =>  


// header, footer, layout



// 5. apps/route/website => route.php

// SEO 