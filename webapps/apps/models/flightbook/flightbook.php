<?php

namespace model;

use gmodel;

class flightbook extends gmodel {

    function __construct() {
        parent::__construct();
    }

    public function getairportsinfo($iatalist) {
        $bookingdatainfo = array();

        $where = array();
        foreach ($iatalist as $iata) {
            $where[] = array('iata', '=', $iata);
            $where[] = 'OR';
        }

        #remove last 'OR' from array
        $listdata = array();
        unset($where[count($where) - 1]);
        if (count($where)) {
            $listdata = $this->db->table('dsairports')->select('id, iata, isocountry as countrycode, municipality as cityname, title as airportname')->where($where)->execute()->result();
        }

        $iatacitycountrydata = array();
        foreach ($listdata as $row) {
            $iatacitycountrydata[$row->iata] = $row;
        }

        $bookingdatainfo = $iatacitycountrydata;

        #get countrylist
        $counrycodelist = array();
        foreach ($bookingdatainfo as $row) {
            $counrycodelist[$row->countrycode] = $row->countrycode;
        }

        $where = array();
        foreach ($counrycodelist as $iata) {
            $where[] = array('iso2', '=', $iata);
            $where[] = 'OR';
        }

        #remove last 'OR' from array
        $listdata = array();
        unset($where[count($where) - 1]);
        if (count($where)) {
            $listdata = $this->db->table('dscountries')->select('id, title, iso2, iso3')->where($where)->execute()->result();
        }

        $countrynamelist = array();
        foreach ($listdata as $row) {
            $countrynamelist[$row->iso2] = $row;
        }

        #set countryname data into iatacitycountrydata
        foreach ($bookingdatainfo as $k => $v) {
            $v->countryname = $countrynamelist[$v->countrycode]->title;
            $bookingdatainfo[$k] = $v;
        }
        return $bookingdatainfo;
    }

    public function getlockresponsedata($usersid, $checkoutsid) {
        $tablename = 'checkouts';
        $where = array(
            array('usersid', '=', $usersid),
            'AND',
            array('id', '=', $checkoutsid)
        );
        $res = $this->db->table($tablename)->select()->where($where)->orderby('id', 'desc')->execute()->row();
        return $res;
    }

    public function updatefare($updatedata, $usersid, $checkoutsid) {
        #update checkout table
        $tablename = 'checkouts';
        $where = array(
            array('usersid', '=', $usersid),
            'AND',
            array('id', '=', $checkoutsid)
        );
        $updated = $this->db->table($tablename)->update($updatedata)->where($where)->execute()->rowaffected();
        if ($updated) {
            #check if data exists in payments table
            $tablename = 'payments';
            $where = array(
                array('usersid', '=', $usersid),
                'AND',
                array('checkoutsid', '=', $checkoutsid)
            );
            $res = $this->db->table($tablename)->select()->where($where)->orderby('id', 'desc')->execute()->row();
            if (isset($res->id)) {
                #now unset lockresponse field if exists
                if (isset($updatedata['lockresponse'])) {
                    unset($updatedata['lockresponse']);
                }
                #now update payments table now
                $updated = $this->db->table($tablename)->update($updatedata)->where($where)->execute()->rowaffected();
            }
        }
        return $updated;
    }

}
