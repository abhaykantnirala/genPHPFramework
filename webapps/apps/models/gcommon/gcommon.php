<?php

namespace model;

use gmodel;

class gcommon extends gmodel {

    function __construct() {
        parent::__construct();
    }

    public function createcancelreferenceno($cancelid, $prefix) {
        $sql = "SELECT CONCAT(edate, '" . $prefix . "', tot) as cancelreferenceno FROM (SELECT COUNT(*) as tot, SUBSTRING(REPLACE(DATE(edate), '-', ''),3) as edate FROM flightcancel WHERE DATE(edate) = (SELECT DATE(edate) as edate FROM `flightcancel` WHERE id = " . $cancelid . ") AND id <= " . $cancelid . ") as a";
        $res = $this->db->inlinequery($sql)->execute()->row();

        $rowaffected = false;
        if (isset($res->cancelreferenceno)) {
            #update same on payments table
            $tablename = 'flightcancel';
            $where = array('id', '=', $cancelid);
            $data = array(
                'cancelreferenceno' => $res->cancelreferenceno
            );
            $rowaffected = $this->db->table($tablename)->update($data)->where($where)->execute()->rowaffected();
        }
        return $res->cancelreferenceno ?? '';
    }

    public function createinvoiceno($transactionsid, $prefix) {
        $sql = "SELECT CONCAT(edate, '" . $prefix . "', tot) as invoiceno FROM (SELECT COUNT(*) as tot, SUBSTRING(REPLACE(DATE(edate), '-', ''),3) as edate FROM transactions WHERE DATE(edate) = (SELECT DATE(edate) as edate FROM `transactions` WHERE id = " . $transactionsid . ") AND id <= " . $transactionsid . ") as a";
        $res = $this->db->inlinequery($sql)->execute()->row();

        $rowaffected = false;
        if (isset($res->invoiceno)) {
            #update same on transactions table
            $tablename = 'transactions';
            $where = array('id', '=', $transactionsid);
            $data = array(
                'invoiceno' => $res->invoiceno
            );
            $rowaffected = $this->db->table($tablename)->update($data)->where($where)->execute()->rowaffected();
        }
        return $res->invoiceno ?? '';
    }

    public function createogorderid($paymentsid, $prefix) {
        $sql = "SELECT CONCAT(edate, '" . $prefix . "', tot) as ogorderid FROM (SELECT COUNT(*) as tot, SUBSTRING(REPLACE(DATE(edate), '-', ''),3) as edate FROM payments WHERE DATE(edate) = (SELECT DATE(edate) as edate FROM `payments` WHERE id = " . $paymentsid . ") AND id <= " . $paymentsid . ") as a";
        $res = $this->db->inlinequery($sql)->execute()->row();

        $rowaffected = false;
        if (isset($res->ogorderid)) {
            #update same on payments table
            $tablename = 'payments';
            $where = array('id', '=', $paymentsid);
            $data = array(
                'ogorderid' => $res->ogorderid
            );
            $rowaffected = $this->db->table($tablename)->update($data)->where($where)->execute()->rowaffected();
        }
        return $res->ogorderid ?? '';
    }

    public function getcancelmsid($cancelid) {
        $tablename = 'flightcancel';
        $where = array('id', '=', $cancelid);
        $res = $this->db->table($tablename)->select('msid')->where($where)->execute()->row();
        return $res->msid ?? 0;
    }

    public function getpaymentsmsid($paymentsid) {
        $tablename = 'payments';
        $where = array('id', '=', $paymentsid);
        $res = $this->db->table($tablename)->select('microservicesid')->where($where)->execute()->row();
        return $res->microservicesid ?? 0;
    }

    public function gettransactionsmsid($transactionsid) {
        $tablename = 'transactions';
        $where = array('id', '=', $transactionsid);
        $res = $this->db->table($tablename)->select('microservicesid')->where($where)->execute()->row();
        return $res->microservicesid ?? 0;
    }

    public function getalltransactionslist($usersid, $date) {
        $where = "";
        if ($date) {
            $where = " AND DATE(payments.edate) < '" . $date . "'";
        }
        $sql = "SELECT transactions.productname, payments.ogorderid, transactions.invoiceno, payments.ordertype, (SELECT s1journeys.source FROM s1journeys WHERE transactionsid = transactions.id ORDER BY s1journeys.id ASC LIMIT 1) as source, (SELECT s1journeys.destination FROM s1journeys WHERE transactionsid = transactions.id ORDER BY s1journeys.id ASC LIMIT 1) as destination, (SELECT s1journeys.pnr FROM s1journeys WHERE transactionsid = transactions.id ORDER BY s1journeys.id ASC LIMIT 1) as pnr, (SELECT DATE(s1journeys.departuredatetime) FROM s1journeys WHERE transactionsid = transactions.id ORDER BY s1journeys.id ASC LIMIT 1) as journeydate, payments.microservicesid, payments.paymentmode, payments.timespoint, payments.nexowalletamount, payments.discount, payments.netamount, payments.device as platform, payments.deviceos as 'devicetype', payments.countrycode, payments.paymentstatus, DATE(payments.edate) as 'transactiondate', transactions.txnstatus as 'servicestatus', usersmemberships.startdate AS 'membershipstartdate', usersmemberships.enddate AS 'membershipenddate', usersmemberships.membershipcode as 'membershipname' FROM payments 
JOIN transactions 
ON 
transactions.paymentsid = payments.id 
LEFT JOIN usersmemberships 
ON 
usersmemberships.paymentsid = payments.id 
WHERE payments.usersid = " . $usersid . " AND (payments.paymentstatus = 'refunded' OR payments.paymentstatus = 'success') AND payments.recevied = 'true'" . $where . " ORDER BY payments.id DESC";
        return $this->db->inlinequery($sql)->execute()->result();
    }

    /*
     * Following is for Elite Pro Users Record Fetching For getting membership-code and capping-limits and used capping amount list
     */

    public function getusedcappingamountlist($usersid) {
        $sql = "SELECT b.usersmembershipsid, b.membershipcode, b.usersmembershipscappingid, b.cappedamount, b.remainingamount, usersmembershipscappingused.amountused, usersmembershipscappingused.paymentsid, (SELECT s1tickets.source FROM s1tickets WHERE transactionsid = (SELECT transactions.id FROM transactions WHERE paymentsid = usersmembershipscappingused.paymentsid) ORDER BY ticketsid ASC LIMIT 1) as source, (SELECT s1tickets.destination FROM s1tickets WHERE transactionsid = (SELECT transactions.id FROM transactions WHERE paymentsid = usersmembershipscappingused.paymentsid) ORDER BY ticketsid ASC LIMIT 1) as destination, (SELECT DATE(s1journeys.departuredatetime) FROM s1journeys WHERE transactionsid = (SELECT transactions.id FROM transactions WHERE paymentsid = usersmembershipscappingused.paymentsid) ORDER BY s1journeys.id ASC LIMIT 1) as journeydate, usersmembershipscappingused.refunded, usersmembershipscappingused.edate, usersmembershipscappingused.mdate, usersmembershipscappingused.id as usersmembershipscappingusedid  FROM (SELECT a.usersmembershipsid, a.membershipcode, IF(a.usersmembershipscappingid IS NULL, 0, a.usersmembershipscappingid) AS usersmembershipscappingid, IF(a.cappedamount IS NULL, 0, a.cappedamount) AS cappedamount, IF(a.remainingamount IS NULL, 0, a.remainingamount) AS remainingamount FROM (SELECT usersmemberships.id as usersmembershipsid, usersmemberships.membershipcode, usersmembershipscapping.id as usersmembershipscappingid, usersmembershipscapping.cappedamount, usersmembershipscapping.remainingamount FROM usersmemberships 
LEFT JOIN usersmembershipscapping
ON
usersmemberships.id = usersmembershipscapping.usersmembershipsid
WHERE 
usersmemberships.usersid = " . $usersid . " AND usersmemberships.issubscribed = 'true' AND usersmemberships.enddate>=DATE(NOW())
ORDER BY usersmemberships.id ASC LIMIT 1) AS a) AS b
JOIN usersmembershipscappingused
ON
usersmembershipscappingused.usersmembershipscappingid = b.usersmembershipscappingid
AND
usersmembershipscappingused.active = 'true'";

        return $this->db->inlinequery($sql)->execute()->result();
    }

    /*
     * Following is for Elite Pro Users Record Fetching For getting membership-code and capping-limits
     */

    public function getexistingeliteprocappinginfo($usersid) {
        $sql = "SELECT a.usersmembershipsid, a.membershipcode, a.infojson, IF(a.usersmembershipscappingid IS NULL, 0, a.usersmembershipscappingid) AS usersmembershipscappingid, IF(a.cappedamount IS NULL, 0, a.cappedamount) AS cappedamount, IF(a.remainingamount IS NULL, 0, a.remainingamount) AS remainingamount FROM (SELECT usersmemberships.id as usersmembershipsid, usersmemberships.membershipcode, usersmemberships.infojson, usersmembershipscapping.id as usersmembershipscappingid, usersmembershipscapping.cappedamount, usersmembershipscapping.remainingamount FROM usersmemberships 
LEFT JOIN usersmembershipscapping
ON
usersmemberships.id = usersmembershipscapping.usersmembershipsid
WHERE 
usersmemberships.usersid = " . $usersid . " AND usersmemberships.issubscribed = 'true' AND usersmemberships.enddate>=DATE(NOW())
ORDER BY usersmemberships.id ASC LIMIT 1) AS a";

        return $this->db->inlinequery($sql)->execute()->row();
    }

    public function updateremainingcappedamount($price, $usersmembershipscappingid, $paymentsid = 0) {
        $tablename = 'usersmembershipscapping';
        $where = array('id', '=', $usersmembershipscappingid);
        $res = $this->db->table($tablename)->select()->where($where)->execute()->row();
        if (isset($res->id)) {
            $remainingamount = $res->remainingamount;
            $remainingamount = ($remainingamount - $price);
            if ($remainingamount < 0) {
                #set price absolute
                $price = $res->remainingamount;
                #set remaining-amount to zero
                $remainingamount = 0;
            }
            #now update remaining price and insert to keep record
            $data = array(
                'remainingamount' => $remainingamount
            );
            $rowaffected = $this->db->table($tablename)->update($data)->where($where)->execute()->rowaffected();
            if ($rowaffected) {
                $tablename = 'usersmembershipscappingused';
                $data = array(
                    'usersmembershipscappingid' => $res->id,
                    'amountused' => $price,
                    'paymentsid' => $paymentsid,
                    'active' => 'true',
                    'refunded' => 'false',
                    'edate' => date('Y-m-d'),
                    'euser' => 'onexo',
                    'mdate' => date('Y-m-d'),
                    'muser' => 'onexo',
                    'statusa' => 1,
                    'statusb' => 0,
                    'statusc' => 0
                );

                $this->db->table($tablename)->insert($data)->execute();
            }
        }
    }

}

#ogpaymentid