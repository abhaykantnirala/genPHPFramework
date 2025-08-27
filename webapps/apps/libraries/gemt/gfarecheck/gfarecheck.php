<?php

namespace library\gemt;

use gcontroller;

class gfarecheck extends gcontroller {

    private $agencyid = '';
    private $ipaddress = '192.168.10.111';
    private $masterkey = '';
    private $password = 'EMT@uytrFYTREt';
    private $portalid = '26';
    private $apiurl = '';
    private $subusertype = 0;
    private $username = 'EMTB2B';
    private $usertype = 0;
    private $oldFare = 0;
    private $newFare = 0;
    private $fareChangeAmount = 0;
    private $oldLockResponse = array();
    private $newlockResponse = array();
    private $oldamount = 0;
    private $oldnetamount = 0;
    private $FlightType = 'Domestic';

    function __construct() {
        parent::__construct();
        $this->load->model('flightbook');
        $this->msurl = $this->config->item('ms-default');
        list($this->apiurl, $this->ipaddress, $this->username, $this->password, $this->portalid) = $this->easemytrip();
    }

    private function easemytrip() {
        $info = array(
            'url' => $this->msurl[14]['url'],
            'data' => (array('passware' => _EASEMYTRIPTOKEN_)),
            'method' => 'POST',
            'return' => true
        );

        #load webcontent
        $msresponse = $this->load->webcontent($info);
        $res = @json_decode($msresponse['data']['response']);
        if (!isset($res->data)) {
            $res = '{"status":"success","data":{"apiurl":"http://stagingapi.easemytrip.com/Flight.svc/json","ipaddress":"192.168.10.111","username":"EMTB2B","password":"EMT@uytrFYTREt","portalid":"26"},"apiaction":{"title":"sandbox","value":0}}';
            $res = json_decode($res);
        } else {
            $res = $res;
        }

        $data = $res->data;
        $response = array(
            $data->apiurl,
            $data->ipaddress,
            $data->username,
            $data->password,
            $data->portalid
        );

        if (isset($res->apiaction) && isset($res->apiaction->title) && isset($res->apiaction->value)) {
            if ($res->apiaction->value == 0) {
                $this->apitype = 'sandbox';
            } else if ($res->apiaction->value == 1) {
                $this->apitype = 'live';
            }
        }

        return $response;
    }

    public function farechek($data) {

        $checkoutsid = $data['checkoutsid'] ?? 0;
        $usersid = $data['usersid'] ?? 0;

        #get checkout data
        $checkoutdata = $this->model->flightbook->getlockresponsedata($usersid, $checkoutsid);

        if (!isset($checkoutdata->id)) {
            return false;
        }

        #check if data is related to flight booking only
        if ($checkoutdata->ordertype != 'servicebooking') {
            return false;
        }

        #get lockresponse data
        $lockresponse = json_decode($checkoutdata->lockresponse);
        $this->oldamount = $checkoutdata->amount;
        $this->oldnetamount = $checkoutdata->netamount;
        $this->oldLockResponse = $lockresponse;
        $guestinfo = json_decode($checkoutdata->guestsinfo);
        $this->TraceId = $checkoutdata->vendortrackid;

        $locktype = $checkoutdata->locktype; #1=>domestic-oneway, 2=>domestic-roundtrip, 3=>international-oneway, 4=>international-roundtrip
        $legsdata = array();

        if ($locktype == 1) { #Domestic-OneWay
            if (isset($lockresponse->Bonds) && is_array($lockresponse->Bonds) && isset($lockresponse->Bonds[0]) && isset($lockresponse->Bonds[0]->Legs)) {
                $this->EngineID = $lockresponse->EngineID;
                $this->CurrencyCode = $lockresponse->CurrencyCode;
                $this->FlightType = 'Domestic-Oneway';
                $legsdata['OutBound'] = array(
                    'lockresponsetripwise' => $lockresponse,
                    'legs' => array(
                        $lockresponse->Bonds
                    ),
                    'EngineID' => $lockresponse->EngineID
                );
                $this->oldFare += $lockresponse->Fare->TotalFareWithOutMarkUp;
            }
        } else if ($locktype == 2) { #Domestic-RoundTrip
            if (isset($lockresponse->ob) && isset($lockresponse->ob->Bonds) && is_array($lockresponse->ob->Bonds) && isset($lockresponse->ob->Bonds[0]) && isset($lockresponse->ob->Bonds[0]->Legs)) {
                $this->EngineID = $lockresponse->ob->EngineID;
                $this->CurrencyCode = $lockresponse->ob->CurrencyCode;
                $this->FlightType = 'Domestic-Roundtrip';
                $legsdata['OutBound'] = array(
                    'lockresponsetripwise' => $lockresponse->ob,
                    'legs' => array(
                        $lockresponse->ob->Bonds
                    ),
                    'EngineID' => $lockresponse->ob->EngineID
                );
                $this->oldFare += $lockresponse->ob->Fare->TotalFareWithOutMarkUp;
            }
            if (isset($lockresponse->ib) && isset($lockresponse->ib->Bonds) && is_array($lockresponse->ib->Bonds) && isset($lockresponse->ib->Bonds[0]) && isset($lockresponse->ib->Bonds[0]->Legs)) {
                $this->EngineID = $lockresponse->ib->EngineID;
                $this->CurrencyCode = $lockresponse->ib->CurrencyCode;
                $this->FlightType = 'Domestic-Roundtrip';
                $legsdata['InBound'] = array(
                    'lockresponsetripwise' => $lockresponse->ib,
                    'legs' => array(
                        $lockresponse->ib->Bonds
                    ),
                    'EngineID' => $lockresponse->ib->EngineID
                );
                $this->oldFare += $lockresponse->ib->Fare->TotalFareWithOutMarkUp;
            }
        } else if ($locktype == 3) { #International-Oneway
            if (isset($lockresponse->Bonds) && is_array($lockresponse->Bonds) && isset($lockresponse->Bonds[0]) && isset($lockresponse->Bonds[0]->Legs)) {
                $this->EngineID = $lockresponse->EngineID;
                $this->CurrencyCode = $lockresponse->CurrencyCode;
                $bondtype = $lockresponse->BondType; # 'OutBound';
                $this->FlightType = 'International-Oneway';
                $legsdata[$bondtype] = array(
                    'lockresponsetripwise' => $lockresponse,
                    'legs' => array(
                        $lockresponse->Bonds
                    ),
                    'EngineID' => $lockresponse->EngineID
                );
                $this->oldFare += $lockresponse->Fare->TotalFareWithOutMarkUp;
            }
        } else if ($locktype == 4) { #International-RoundTrip
            if (isset($lockresponse->Bonds) && is_array($lockresponse->Bonds) && isset($lockresponse->Bonds[0]) && isset($lockresponse->Bonds[0]->Legs)) {
                $this->EngineID = $lockresponse->EngineID;
                $this->CurrencyCode = $lockresponse->CurrencyCode;
                $this->FlightType = 'International-Roundtrip';
                $legsdata['OutBound'] = array(
                    'lockresponsetripwise' => $lockresponse,
                    'legs' => array(
                        $lockresponse->Bonds
                    ),
                    'EngineID' => $lockresponse->EngineID
                );
                $this->oldFare += $lockresponse->Fare->TotalFareWithOutMarkUp;
            }
        }

        if (count($legsdata) == 0) {
            return false;
        }

        $requestlist = array();
        #now loop through each trip (outbounds and inbounds)
        foreach ($legsdata as $triptype => $lockresponselegsinfo) {
            $legsinfo = $lockresponselegsinfo['legs'];
            $triplockresponse = $lockresponselegsinfo['lockresponsetripwise'];
            $EngineID = $lockresponselegsinfo['EngineID'];
            $requestlist[$triptype] = $this->getfarecheckrequest($triptype, $legsinfo, $triplockresponse, $EngineID, $guestinfo);
        }

        $res = $this->getpricerecheckresponse($requestlist, $locktype, $checkoutsid, $usersid);
        return $res;
    }

    private function getpricerecheckresponse($requestlist, $locktype, $checkoutsid, $usersid) {
        $reqestinfo = array();
        foreach ($requestlist as $triptype => $row) {

            $reqestinfo[$triptype] = array(
                'url' => $this->apiurl . '/AirRePriceRQ', #'AirRePriceRQ',
                'method' => 'POST',
                'header' => array('Content-Type:application/json'),
                'data' => json_encode($row)
            );
        }

        $res = $this->load->webcontent($reqestinfo);

        if (isset($res['data']['request'])) {
            $reqres = $res['data'];
            $res['data'] = array();
            $res['data']['OutBound'] = $reqres;
        }

        #now calculate new fare
        foreach ($res['data'] as $triptype => $row) {
            $row = json_decode($row['response']);
            #check if data is not found then return false;
            if (isset($row->Errors->Code)) {
                return false;
            }
            $this->newlockResponse[$triptype] = $row;

            if ($locktype == 1) { #Domestic-OneWay
                $this->newFare += $row->Journeys[0]->Segments[0]->Fare->TotalFareWithOutMarkUp;
            } else if ($locktype == 2) { #Domestic-Roundtrip
                $this->newFare += $row->Journeys[0]->Segments[0]->Fare->TotalFareWithOutMarkUp;
            } else if ($locktype == 3) { #International-OneWay
                $this->newFare += $row->Journeys[0]->Segments[0]->Fare->TotalFareWithOutMarkUp;
            } else if ($locktype == 4) { #International-Roundtrip
                $this->newFare += $row->Journeys[0]->Segments[0]->Fare->TotalFareWithOutMarkUp;
            }
        }
        $updated = false;
        //$this->newFare -= 615;
        #now get farechangeamount
        $this->fareChangeAmount = $this->newFare - $this->oldFare;

        if ($this->fareChangeAmount != 0) {
            #update here fare in checkout table  and payment table
            $updated = $this->updatefareinformation($this->newlockResponse, $this->oldLockResponse, $this->fareChangeAmount, $locktype, $checkoutsid, $usersid);
        }

        $farechanged = ($this->fareChangeAmount == 0) ? false : true;
        $changesymbol = '';
        if ($this->fareChangeAmount > 0) {
            $changesymbol = '+';
        } else if ($this->fareChangeAmount < 0) {
            $changesymbol = '-';
        }

        $finalresponse = array(
            'farechanged' => $farechanged,
            'changesymbol' => $changesymbol, #'+/-/',
            'amount' => abs($this->fareChangeAmount),
        );

        return $finalresponse;
    }

    private function updatefareinformation($newlockResponse, $oldLockResponse, $fareChangeAmount, $locktype, $checkoutsid, $usersid) {
        $fares = array();
        if ($locktype == 1) {
            $fares['OutBound'] = $newlockResponse['OutBound']->Journeys[0]->Segments[0]->Fare;
            $oldLockResponse->Fare = $fares['OutBound'];
        } else if ($locktype == 2) {
            $fares['OutBound'] = $newlockResponse['OutBound']->Journeys[0]->Segments[0]->Fare;
            $fares['InBound'] = $newlockResponse['InBound']->Journeys[0]->Segments[0]->Fare;
            $oldLockResponse->ob->Fare = $fares['OutBound'];
            $oldLockResponse->ib->Fare = $fares['InBound'];
        } else if ($locktype == 3) {
            $fares['OutBound'] = $newlockResponse['OutBound']->Journeys[0]->Segments[0]->Fare;
            $oldLockResponse->Fare = $fares['OutBound'];
        } else if ($locktype == 4) {
            $fares['OutBound'] = $newlockResponse['OutBound']->Journeys[0]->Segments[0]->Fare;
            $oldLockResponse->Fare = $fares['OutBound'];
        }

        $amount = $this->oldamount + $fareChangeAmount;
        $netamount = $this->oldnetamount + $fareChangeAmount;

        $updatedata = array(
            'lockresponse' => json_encode($oldLockResponse),
            'amount' => $amount,
            'netamount' => $netamount
        );

        #update here data in checkout table
        $updated = $this->model->flightbook->updatefare($updatedata, $usersid, $checkoutsid);
        return $updated;
    }

    private function getfarecheckrequest($triptype, $legsinfo, $triplockresponse, $EngineID, $guestinfo) {
        $Adults = 0;
        $Childs = 0;
        $Infants = 0;

        #get paxinfo 
        if (is_array($guestinfo)) {
            foreach ($guestinfo as $row) {
                if (isset($row->pt)) {
                    if ($row->pt == 0) {
                        $Adults++;
                    } else if ($row->pt == 1) {
                        $Childs++;
                    } else if ($row->pt == 2) {
                        $Infants++;
                    }
                }
            }
        }

        if ($Adults == 0 && $Childs == 0 && $Infants == 0) {
            $Adults = 1;
        }

        $FlightSearchDetails = $this->FlightSearchDetails($legsinfo);
        $request = array(
            'FlightAvailabilityRQ' => array(
                "Adults" => $Adults, #D=1 #MAX=9
                "Childs" => $Childs, #D=0 #MAX=9
                "Infants" => $Infants, #D=0 #MAX=2
                "Cabin" => $extradata['flightclass'] ?? 0, #D=0
                "EngineID" => array($EngineID),
                "FlightSearchDetails" => $FlightSearchDetails,
                "TraceId" => $this->TraceId,
                "SaveSessionStatus" => true,
                "TripType" => 0,
                "AirpricePosition" => 1
            ),
            'Segment' => array(
                $triplockresponse
            )
        );

        if ($this->FlightType == 'Domestic-Oneway') {
            $request['FlightAvailabilityRQ']['TripType'] = 0;
            $request['FlightAvailabilityRQ']['AirpricePosition'] = 1;
        } else if ($this->FlightType == 'Domestic-Roundtrip') {
            $request['FlightAvailabilityRQ']['TripType'] = 1;
            $request['FlightAvailabilityRQ']['AirpricePosition'] = 1;
        } else if ($this->FlightType == 'International-Oneway') {
            $request['FlightAvailabilityRQ']['TripType'] = 0;
            $request['FlightAvailabilityRQ']['AirpricePosition'] = 1;
        } else if ($this->FlightType == 'International-Roundtrip') {
            $request['FlightAvailabilityRQ']['TripType'] = 1;
            $request['FlightAvailabilityRQ']['AirpricePosition'] = 1;
            $request['FlightAvailabilityRQ']['IsDomestic'] = false;
            $request['FlightAvailabilityRQ']['IsInternational'] = true;
        }

        $request['FlightAvailabilityRQ'] = array_merge($this->apiauthentication(), $request['FlightAvailabilityRQ']);

        return $request;
    }

    private function apiauthentication() {
        return array("Authentication" => array(
                "AgencyId" => $this->agencyid,
                "IpAddress" => $this->ipaddress,
                "MasterKey" => $this->masterkey,
                "Password" => $this->password,
                "PortalID" => $this->portalid,
                "SubUserType" => $this->subusertype,
                "UserName" => $this->username,
                "UserType" => $this->usertype
            )
        );
    }

    private function FlightSearchDetails($legsinfo) {
        $array = array();
        foreach ($legsinfo[0] as $row) {
            $legs = $row->Legs;
            $destination = $legs[count($legs) - 1]->Destination ?? '';
            $origin = $legs[0]->Origin ?? '';
            $ddate = date("Y-m-d", strtotime(str_replace('-', '', $legs[0]->DepartureDate)));
            $array[] = array(
                'BeginDate' => $ddate, #R
                "CurrencyCode" => $this->CurrencyCode ?? 'INR', #R #D=INR
                "Destination" => $destination, #R
                "Origin" => $origin  #R
            );
        }
        return $array;
    }

}
