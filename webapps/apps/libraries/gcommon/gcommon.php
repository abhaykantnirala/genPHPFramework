<?php

namespace library;

use gcontroller;

class gcommon extends gcontroller {

    private $msidlistdetails = array('1' => 'Flight Book', '2' => 'Flight Reschedule', '3' => 'Flight Cancel', '82' => 'Nexo Elite Membership Subscription');
    private $misdlist = array('1' => 'FB', '2' => 'FR', '3' => 'FC', '82' => 'ES');
    private $orderidprefix = 'T';
    private $invoicenoprefix = 'G';

    function __construct() {
        parent::__construct();
        $this->load->model('gcommon');
        $this->msurl = $this->config->item('ms-default');
    }

    public function createcancelreferenceno($cancelid) {
        $msid = $this->model->gcommon->getcancelmsid($cancelid);
        if (!isset($this->misdlist[$msid])) {
            die('invalid msid');
        }
        $prefix = $this->orderidprefix . $this->misdlist[$msid];
        $cancelreferenceno = $this->model->gcommon->createcancelreferenceno($cancelid, $prefix);
        return $cancelreferenceno;
    }

    public function createogorderid($paymentsid) {
        $msid = $this->model->gcommon->getpaymentsmsid($paymentsid);
        if (!isset($this->misdlist[$msid])) {
            die('invalid msid');
        }
        $prefix = $this->orderidprefix . $this->misdlist[$msid];
        $ogorderid = $this->model->gcommon->createogorderid($paymentsid, $prefix);
        return $ogorderid;
    }

    public function createinvoiceno($transactionsid) {
        $msid = $this->model->gcommon->gettransactionsmsid($transactionsid);
        if (!isset($this->misdlist[$msid])) {
            die('invalid msid');
        }
        $prefix = $this->invoicenoprefix . $this->misdlist[$msid];
        $ogcreateinvoiceno = $this->model->gcommon->createinvoiceno($transactionsid, $prefix);
        return $ogcreateinvoiceno;
    }

    public function getservicecommission($data) {
        $commission = 0;
        $info = array(
            'url' => $this->msurl[63]['url'] . "getonexoallmircroservfees", #"https://eco.onexo.app/getonexofees"
            'method' => 'post',
            'return' => true,
            'data' => json_encode($data),
            'header' => array("Content-Type:application/json", "Authorization:Basic YWRtaW46YWRtaW4=")
        );

        $res = $this->load->webcontent($info);

        if (isset($res['data']['response'])) {
            $res = @json_decode($res['data']['response']);
        }

        if (isset($res->status) && strtolower($res->status) == 'success') {
            $commission = $res->data->normalfees ?? 249;
        }

        return $commission;
    }

}
