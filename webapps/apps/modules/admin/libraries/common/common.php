<?php

namespace library;

use gcontroller;

class common extends gcontroller {

    function __construct() {
        parent::__construct();
        $this->load->model('common');
    }

    public function getuserslog($deviceid) {
        $res = $this->model->common->getuserslog($deviceid);
        return $res['data'] ?? array();
    }

    public function msflowdata($deviceid) {
        $response = array();
        #get msflow data
        $res = $this->model->common->msflowdata($deviceid);
        
        #get microservice name list
        $mslist = array(
            "g00" => 'Gateway',
            "g01" => "tokengenerator",
            "g02" => "Decryption",
            "g03" => 'Logs Generator',
            "g04" => "Encryption",
            "g05" => "Version-check-Layer",
            "g06" => "Interface-Validation",
            "g07" => "Users-validation",
            "g08" => "CT Check Layer",
            "g09" => "restriction-layer",
            "g10" => "NLU",
            "g11" => "NER",
            "g12" => "Continued-Conversion",
            "g13" => "CT-Manager",
            "g14" => "Registration",
            "g15" => "service-layer",
            "g16" => "payment-gateways-layer",
            "g17" => "tickets-layer",
            "g18" => "logs",
            "g19" => "mirror-layer",
            "g20" => "proto-buff-layer",
            "g21" => "compression-layer",
            "g22" => "cc",
            "g23" => "Services",
            "g24" => "aaa-24",
            "g25" => "aaa-25"
        );
        #add here microservice name
        foreach ($res as $k => $row) {
            $row->microservicename = $mslist[$row->msid] ?? 'Unknown';
            $res[$k] = $row;
        }

        #add gateway name in list
        $arr = array(
            'id' => 0,
            'pId' => 0,
            'name' => 'Gateway',
            'color' => 'green',
            'open' => true
        );
        $response[] = $arr;
        #create tree-view json
        foreach ($res as $key => $row) {
            $parentsid = explode('-', $row->parentid);
            $cnt = -1;
            foreach ($parentsid as $pid) {
                $cnt++;
                $parentid = $pid;
                if (isset($parentsid[$cnt + 1])) {
                    $childid = $parentsid[$cnt + 1];
                    $msname = $mslist[$childid] ?? 'Unknown';
                    $arr = array(
                        'id' => $childid,
                        'pId' => $parentid,
                        'name' => $msname,
                        'color' => $row->iserror == 'false' ? 'green' : 'red',
                        'open' => true
                    );
                    $response[] = $arr;
                }
            }
        }

        echo json_encode($response);
        die;
    }

}

//[
//      {id: 1, pId: 0, name: "Basic Functions", open: true},
//      {id: 101, pId: 1, name: "Standard JSON Data", file: "core/standardData"},
//      {id: 102, pId: 1, name: "Simple JSON Data", open: true},
//      {id: 103, pId: 1, name: "Don't Show Line", file: "core/noline"},
//      {id: 104, pId: 102, name: "Don't Show Icon", file: "core/noicon"}
//]
