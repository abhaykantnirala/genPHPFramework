<?php

namespace library\gemt;

use gcontroller;

class pendingpnr extends gcontroller {

    function __construct() {
        parent::__construct();
        $this->load->model('flightbook');
    }

    public function getbookingstatus() {

        $request = array(
            "Authentication" => array(
                "IpAddress" => "192.168.10.111",
                "Password" => "EMT@uytrFYTREt",
                "PortalID" => 26,
                "UserName" => "EMTB2B"
            ),
            "transactionScreenId" => "EMT599926668"
        );

        $request = json_encode($request);

        $url = 'https://stagingapi.easemytrip.com/cancellationjson/api/flightbookingdetail';

        $info = array(
            'url' => $url,
            'data' => $request,
            'method' => 'post',
            'header' => array(
                'Content-Type:application/json'
            )
        );
        $res = $this->load->webcontent($info);
        $res = json_decode($res['data']['response']);

        echo '<pre>';

        $airportinformation = $this->airportinformation($res);


        print_r($res);
    }

    private function airportinformation($res) {
        $iatalist = array();

        foreach ($res->passengerDetails as $row) {
            $iatalist[$row->origin] = $row->origin;
            $iatalist[$row->destination] = $row->destination;
        }

        $info = array();
        #now get iata information
        $info['iatainformation'] = $this->model->flightbook->getairportsinfo($iatalist);
        #now get journey type information
        $info['tripType'] = $this->gettripinformation($res);
        #now get domestic-international information
        $info['journeytype'] = $this->getjourneyinformation($info['iatainformation']);

        $aa = json_decode($this->config->item(implode('-', array($info['journeytype'], $info['tripType']))));
        echo '<pre>';
        print_r($aa);
        echo '<hr>';

        echo '<hr>';
        print_r($info);
        die;


        return $iatalist;
    }

    private function gettripinformation($res) {
        $journeytype = 'oneway';
        foreach ($res->passengerDetails as $row) {
            if ($row->tripType == 'InBound') {
                $journeytype = 'roundtrip';
                break;
            }
        }
        return $journeytype;
    }

    private function getjourneyinformation($iatainformation) {
        $journeytype = 'domestic';
        foreach ($iatainformation as $row) {
            if ($row->countrycode != 'IN') {
                $journeytype = 'international';
                break;
            }
        }
        return $journeytype;
    }

}
