<?php

class _els extends gcontroller {

    private $url = 'http://localhost:9200/';
    private $data = array();

    function __construct() {
        parent::__construct();
        $this->load->model('els');
    }

    public function login() {
        if ($this->library->session->getdata('login') == 'yes') {
            $this->helper->url->redirect('_els');
        }
        $this->load->view('login');
    }

    private function aes($data) {
        $ciphertext_dec = base64_decode($data);
        $key = hex2bin(bin2hex("onexoonexoonexoo"));
        $iv_dec = hex2bin("abcdef9876543210abcdef9876543210");
        $decrypteddata = openssl_decrypt($ciphertext_dec, 'AES-128-CBC', $key, OPENSSL_RAW_DATA | OPENSSL_ZERO_PADDING, $iv_dec);
        return trim($decrypteddata);
    }

    public function index() {
        $this->session->setdata('login', 'yes');
        if (!$this->session->getdata('login')) {
            $email = $this->aes($this->helper->input->post('email'));
            $password = $this->aes($this->helper->input->post('password'));
            if ($email == 'elsadmin@onexo.app' && $password == 'nexo@#12345!') {
                $this->session->setdata('login', 'yes');
            } else {
                $this->helper->url->redirect('_els-login');
            }
        }

        $this->data['leftside'] = $this->getleftsidedata();
        $this->data['_menu_'] = $this->getmenu('database-menu');
        $this->data['_elsinfo_'] = $this->model->els->getelsinfo();
        $this->load->layout->els('index', $this->data);
    }

    public function fetchdata() {
        $action = $this->helper->input->post('action');
        $res = '<h1>There is some error</h1>';
        switch ($action) {
            case 'index=list': $res = $this->fetchindexlist();
                break;
            case 'index=delete': $res = $this->deleteindex();
                break;
            case 'index-create': $res = $this->createindex();
                break;
            case 'index=indexlist': $res = $this->getindextypelist();
                break;
            case 'index=indexdata': $res = $this->getindextypedata(); #show index data
                break;
            case 'indextypedoc=delete': $res = $this->deleteindextypedoc();
                break;
            case 'indexstructure=view': $res = $this->getindexstructure();
                break;
            case 'indexstructure=save': $res = $this->createindexstructure();
                break;
            case 'index=sort-view': $res = $this->sortindexdata();
                break;
            case 'indexoperation=view': $res = $this->getindexsoperationpage();
                break;
            case 'index=truncate': $res = $this->truncateindex();
                break;
            case 'indexinsert=view': $res = $this->getindexinsertview();
                break;
            case 'indexinsert=save': $res = $this->saveindexinsertviewdata();
                break;
        }
        echo $res;
    }

    private function saveindexinsertviewdata() {
        $indexname = $this->helper->input->post('indexname');
        $data = $this->helper->input->post('data');
        #create data array for insert
        $dataarr = array();
        foreach ($data as $row) {
            $dataarr[$row['name']] = $row['value'];
        }

        $data = array();
        $data[] = $dataarr;

        $queryresponse = $this->model->els->saveindexinsertviewdata($indexname, $data);

        $output = array(
            'status' => 'success',
            'message' => 'Data inserted Successfully',
            'data' => $queryresponse
        );
        return json_encode($output);
    }

    private function getindexinsertview() {
        $indexname = $this->helper->input->post('indexname');
        $this->data['indexname'] = $indexname;
        $this->data['indexstructureinfo'] = $this->model->els->getindexfieldlist($indexname);

        $output = array(
            'status' => 'success',
            'message' => 'Showing Index Insert fields',
            'data' => $this->load->view('index-insert', $this->data, true)
        );
        return json_encode($output);
    }

    private function truncateindex() {
        $indexname = $this->helper->input->post('indexname');
        $this->data['indexname'] = $indexname;

        $res = $this->model->els->truncateindex($indexname);

        #detect error while deleting index from ElasticSearch DB
        if (isset($res->error)) {
            $output = array(
                'status' => 'fail',
                'message' => 'Index ' . $indexname . ' not truncated.\n' . $res->error->reason
            );
            return json_encode($output);
        }

        #everything is okay
        $output = array(
            'status' => 'success',
            'message' => 'Index ' . $indexname . ' truncated sucessfully'
        );
        return json_encode($output);
    }

    private function getindexsoperationpage() {
        $indexname = $this->helper->input->post('indexname');
        $this->data['indexname'] = $indexname;

        $output = array(
            'status' => 'success',
            'message' => 'Operation page of ' . $indexname . ' generated sucessfully',
            'data' => $this->load->view('index-operation-view', $this->data, true)
        );
        return json_encode($output);
    }

    private function sortindexdata() {
        $indexname = $this->helper->input->post('indexname');
        $fieldname = $this->helper->input->post('fieldname');
        $sortby = $this->helper->input->post('sortby');


        if (strtolower($sortby) == 'asc') {
            $sortby = 'desc';
        } else if (strtolower($sortby) == 'desc') {
            $sortby = 'asc';
        } else {
            $sortby = 'asc';
        }

        $this->session->setdata('fieldname', $fieldname);
        $this->session->setdata('sortby', $sortby);

        $sortinfo = array($fieldname, $sortby);
        return $this->getindextypedata($sortinfo);
    }

    private function createindexstructure() {
        $indexname = $this->helper->input->post('indexname');
        $data = json_decode($this->helper->input->post('data'));
        $res = $this->model->els->createindexstructure($indexname, $data);

        #detect error while deleting index from ElasticSearch DB
        if (isset($res->error)) {
            $output = array(
                'status' => 'fail',
                'message' => 'Index structure of ' . $indexname . ' not created.\n' . $res->error->reason
            );
            return json_encode($output);
        }

        #everything is okay
        $output = array(
            'status' => 'success',
            'message' => 'Index structure of ' . $indexname . ' created sucessfully'
        );
        return json_encode($output);
    }

    private function getindexstructure() {
        $indexname = $this->helper->input->post('indexname');
        $this->data['indexname'] = $indexname;
        $this->data['indexstructureinfo'] = $this->model->els->getindexfieldlist($indexname);

        $output = array(
            'status' => 'success',
            'message' => 'Index structure of ' . $indexname . ' generated sucessfully',
            'data' => $this->load->view('index-structure', $this->data, true)
        );
        return json_encode($output);
    }

    private function deleteindextypedoc() {
        $indexname = $this->helper->input->post('indexname');
        $_id = $this->helper->input->post('_id');
        #delete data
        $res = $this->model->els->deleteindextypedoc($indexname, $_id);
        #create output array
        $output = array();
        #set message for delete operation
        $output['message'] = 'Index data of ' . $indexname . ' delete sucessfully';
        #return result
        return json_encode($output);
    }

    private function getindextypedata($sortinfo = array()) {
        $indexname = $this->helper->input->post('indexname');
        $this->data['indexdata'] = $this->model->els->getindextypedata($indexname, $sortinfo);
        $topmenu = $this->load->view('menu', array('indexname' => $indexname), true);
        $this->data['indexname'] = $indexname;


        $output = array(
            'status' => 'success',
            'message' => 'Index data of ' . $indexname . ' generated sucessfully',
            'data' => $this->load->view('indextypedata', $this->data, true),
            'topmenu' => $topmenu
        );
        return json_encode($output);
    }

    private function getindextypelist() {
        $indexname = $this->helper->input->post('indexname');

        $output = array(
            'status' => 'success',
            'message' => 'Index list of ' . $indexname . ' created sucessfully',
            'data' => $this->fetchindextypelist()
        );
        return json_encode($output);
    }

    private function fetchindextypelist() {
        $indexname = $this->helper->input->post('indexname');
        $this->data['indextypelist'] = array();
        return $this->load->view('indextypelist', $this->data, true);
    }

    private function getmenu($menutype = 'database-menu') {
        switch ($menutype) {
            case 'database-menu' : return $this->load->view('menu', array(), true);
                break;
        }
    }

    private function createindex() {
        $indexname = $this->helper->input->post('indexname');
        $numberofshards = (int) $this->helper->input->post('numberofshards');
        $numberofreplicas = (int) $this->helper->input->post('numberofreplicas');
        $res = $this->model->els->createindex($indexname, $numberofshards, $numberofreplicas);
        #detect error while deleting index from ElasticSearch DB
        if (isset($res->error)) {
            $output = array(
                'status' => 'fail',
                'message' => 'Index ' . $indexname . ' not created.\n' . $res->error->reason,
                'data' => ''
            );
            return json_encode($output);
        }

        #everything is okay
        if (isset($res->acknowledged) && $res->acknowledged == 1) {
            $output = array(
                'status' => 'success',
                'message' => 'Index ' . $indexname . ' created sucessfully',
                'data' => $this->fetchindexlist(),
                'leftside' => $this->getleftsidedata()
            );
            return json_encode($output);
        }
    }

    private function deleteindex() {
        $indexname = $this->helper->input->post('indexname');
        $res = $this->model->els->deleteindex($indexname);
        #detect error while deleting index from ElasticSearch DB
        if (isset($res->error)) {
            $output = array(
                'status' => 'fail',
                'message' => 'Index ' . $indexname . ' not found',
                'data' => ''
            );
            return json_encode($output);
        }
        #everything is okay
        if (isset($res->acknowledged) && $res->acknowledged == 1) {
            $output = array(
                'status' => 'success',
                'message' => 'Index ' . $indexname . ' deleted sucessfully',
                'data' => $this->fetchindexlist(),
                'leftside' => $this->getleftsidedata()
            );
            return json_encode($output);
        }
    }

    private function getleftsidedata() {
        $this->data['indexlist'] = $this->model->els->getindexlist();
        return $this->load->view('leftside', $this->data, true);
    }

    private function fetchindexlist() {
        $data['indexlist'] = $this->model->els->getindexlist();
        return $this->load->view('indexlist', $data, TRUE);
    }

}
