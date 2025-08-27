<?php

namespace library;

use gcontroller;

class currencyconvertor extends gcontroller {

    private $updatehours = 12;
    private $url = 'http://apilayer.net/api/live?access_key=e8e76253d4cb7ca7731f06b591776516';
    private $filepath = 'resources/currencyapidata/currency.json';
    private $roundfigureafterdecimal = 3;

    function __construct() {
        parent::__construct();
    }

    public function response($from = 'INR', $to = 'USD', $value = 0) {
        $from = strtoupper($from);
        $to = strtoupper($to);
        $value = abs($value);

        $path = $this->helper->url->currentroot($this->filepath);
        $fileinfo = $this->helper->file->getfileinfo($path);
        if (!$fileinfo) {
            #file not exists
            #now update apidata
            $this->updateapidata($path);
            $fileinfo = $this->helper->file->getfileinfo($path);
        }
        #now check if it is time to update currency api data
        #get file time
        $mtime = $fileinfo->mtime;
        #get current time
        $ctime = time();
        #get time differenct
        $timediff = $ctime - $mtime;
        #if required then update api data
        if ($timediff - $this->updatehours * 3600 > 0) {
            #now update apidata
            $this->updateapidata($path);
        }
        #now get api data
        $odata = $this->load->file($path, true);
        $data = @json_decode($odata);
        #check if data exists in well format
        if (!isset($data->quotes)) {
            #data is in invalid format
            echo '<h2>Currency Data is in valid format</h2><br>';
            echo 'Data found in currency api json on path <span style="color:blue;">' . $path . '</span> is as following...<br>';
            echo '<pre>';
            print_r($odata);
            echo '</pre>';
            die();
        }

        #check if currency code is valid
        if (!isset($data->quotes->{'USD' . $to}) || !isset($data->quotes->{'USD' . $from})) {
            die('Invalid currency code');
        }

        #everything is okay now do currency conversion
        #now get numerator
        $numerator = $data->quotes->{'USD' . $to};
        #now get demominator
        $denominator = $data->quotes->{'USD' . $from};
        #now get multiplier
        $multiplier = $value;
        #now do conversion and get round data with decimal value
        $cvalue = round(($numerator * $multiplier) / $denominator, $this->roundfigureafterdecimal);
        #create response
        $res = (object) array(
                    'from' => $from,
                    'to' => $to,
                    'value' => $value,
                    'cvalue' => $cvalue
        );
        #return response output object
        return $res;
    }

    private function updateapidata($path) {
        #get api response
        $apiresponse = $this->load->webcontent(array('url' => $this->url));
        #convert to php object
        $response = @json_decode($apiresponse['data']['response']);
        #check if data found in response array
        if (isset($response->quotes)) {
            #save api data to file
            $this->helper->file->save($path, $apiresponse['data']['response']);
        }
    }

}
