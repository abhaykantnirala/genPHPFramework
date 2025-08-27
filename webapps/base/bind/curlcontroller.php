<?php

class curlcontroller {

    private $defaulttimeout = 180;

    function __construct() {
        
    }

    /**
     * 
     * @param type $info = [
      {
      "url": "http://localhost",
      "method": "post",
      "data": "Test Data",
      "return": true,
      "header": [],
      "auth": ""
      }
      ]
     * @return type array(status=>success, data=>[data], exetime=>0.034)
     */
    public function webcontent($info, $data = array(), $method = 'get', $headers = array(), $auths = '') {

        #check for old version 
        if (!is_array($info)) {
            return $this->_webcontent_old($info, $data, $method, $headers, $auths);
        }

        #check for single array
        if (isset($info['url'])) {
            $info = array($info);
        }

        #checking for valid url
        foreach ($info as $row) {
            $url = isset($row['url']) ? $row['url'] : false;

            #message for missing web url
            if (!$url) {
                $info = '<pre>' . print_r($info, true) . '</pre>';
                die("Warning: Missing web url in <br>" . $info);
            }

            #validate url
            if (!filter_var($url, FILTER_VALIDATE_URL)) {
                $info = '<pre>' . print_r($info, true) . '</pre>';
                die("Warning: Invalid web url <h4>{$url}</h4> in following <br>" . $info);
            }
        }

        #seperate without wait response
        $newinfo = array();
        foreach ($info as $i => $row) {
            $return = isset($row['return']) ? $row['return'] : true;
            if ($return != true) {
                //unset($info[$i]);
                $newinfo[] = $row;
            }
        }

        if (count($info)) {
            return $this->_webcontent($info);
        }
    }

    private function _webcontent($info) {
        /**
         * everything is okay now move ahead 
         * */
        #start time
        $stm = microtime(TRUE);
        #Start multi-curl operation
        $multicurl = array();
        #data to be returned
        $response = array();
        #multi handle
        $mh = curl_multi_init();

        foreach ($info as $i => $row) {

            $url = isset($row['url']) ? trim($row['url']) : false;
            $method = strtoupper(isset($row['method']) ? $row['method'] : 'GET');
            $data = isset($row['data']) ? $row['data'] : array();
            $return = isset($row['return']) ? $row['return'] : true; #default value is true
            $header = isset($row['header']) ? $row['header'] : array();
            $auth = isset($row['auth']) ? $row['auth'] : false;
            $curlversion = isset($row['curlversion']) ? $row['curlversion'] : '';
            $useragent = isset($row['useragent']) ? $row['useragent'] : '';
            $timeout = isset($row['timeout']) ? $row['timeout'] : (ini_get('max_execution_time') > $this->defaulttimeout ? $this->defaulttimeout : (ini_get('max_execution_time') - 5));
            $SSLCERT = isset($row['SSLCERT']) ? $row['SSLCERT'] : '';
            $SSLKEY = isset($row['SSLKEY']) ? $row['SSLKEY'] : '';

            #update method
            $info[$i]['method'] = $method;

            if (in_array($method, array('GET'))) {
                #check if already question marked added to url
                $urlparse = parse_url($url);
                if (is_array($data)) {
                    if (isset($urlparse['query'])) {
                        $url = $url . (count($data) ? '&' . http_build_query($data) : '');
                    } else {
                        #check if ? exists at last
                        if (substr(trim($url), -1) == '?') {
                            # symbol ? exists so don't add it
                            $url = $url . (count($data) ? http_build_query($data) : '');
                        } else {
                            # symbol ? does't exists so add it
                            if (trim((count($data) ? http_build_query($data) : ''))) {
                                $url = $url . '?' . (count($data) ? http_build_query($data) : '');
                            }
                        }
                    }
                    #update data
                    $info[$i]['data'] = '';
                } else {
                    #update data
                    $info[$i]['data'] = $data;
                }
                #update url
                $info[$i]['url'] = $url;
            } else if ($method == 'POST') {
                $url = $url;
                #check if array is multi-dimentional
                if (is_array($data)) {
                    if (count($data) != count($data, COUNT_RECURSIVE)) {
                        $data = json_encode($data);
                    }
                }
            } else if (in_array($method, array('PUT', 'PATCH'))) {
                $url = $url;
                if (is_array($data)) {
                    $data = json_encode($data);
                }
            }

            $multicurl[$i] = curl_init();

            curl_setopt($multicurl[$i], CURLOPT_URL, $url);
            curl_setopt($multicurl[$i], CURLOPT_HEADER, 0);

            if ($SSLCERT) {
                curl_setopt($multicurl[$i], CURLOPT_SSLCERT, $SSLCERT);
            }

            if ($SSLKEY) {
                curl_setopt($multicurl[$i], CURLOPT_SSLKEY, $SSLKEY);
            }

            if ($curlversion == '1_0') {
                curl_setopt($multicurl[$i], CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
            } else if ($curlversion == '1_1') {
                curl_setopt($multicurl[$i], CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
            } else if ($curlversion == '2_0') {
                curl_setopt($multicurl[$i], CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_2_0);
            } else if ($curlversion == '2TLS') {
                curl_setopt($multicurl[$i], CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_2TLS);
            } else if ($curlversion == '2_PRIOR_KNOWLEDGE') {
                curl_setopt($multicurl[$i], CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_2_PRIOR_KNOWLEDGE);
            } else if ($curlversion == '3') {
                curl_setopt($multicurl[$i], CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_3);
            } else {
                curl_setopt($multicurl[$i], CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_NONE);
            }

            if ($useragent) {
                curl_setopt($multicurl[$i], CURLOPT_USERAGENT, $useragent);
            }

            curl_setopt($multicurl[$i], CURLOPT_CUSTOMREQUEST, $method);

            if (is_array($header) && count($header)) {
                curl_setopt($multicurl[$i], CURLOPT_HTTPHEADER, $header);
            }

            if (in_array($method, array('POST', 'PUT', 'PATCH', 'DELETE'))) {
                curl_setopt($multicurl[$i], CURLOPT_POST, 1);
                curl_setopt($multicurl[$i], CURLOPT_POSTFIELDS, $data);
            }

            if (is_string($auth) && strlen(trim($auth))) {
                curl_setopt($multicurl[$i], CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
                curl_setopt($multicurl[$i], CURLOPT_USERPWD, $auth);
            }

            curl_setopt($multicurl[$i], CURLOPT_RETURNTRANSFER, 1);

            if ($return) {
                curl_setopt($multicurl[$i], CURLOPT_TIMEOUT, $timeout);
            } else {
                curl_setopt($multicurl[$i], CURLOPT_NOSIGNAL, 1); #Very important which tell curl not wait for response
                curl_setopt($multicurl[$i], CURLOPT_TIMEOUT_MS, 250); #Very important which tell curl not wait for response
                curl_setopt($multicurl[$i], CURLOPT_FORBID_REUSE, 1);
                curl_setopt($multicurl[$i], CURLOPT_CONNECTTIMEOUT_MS, 250);
                curl_setopt($multicurl[$i], CURLOPT_DNS_CACHE_TIMEOUT, 1);
                curl_setopt($multicurl[$i], CURLOPT_FRESH_CONNECT, 1);
            }

            curl_setopt($multicurl[$i], CURLOPT_FOLLOWLOCATION, TRUE);

            curl_multi_add_handle($mh, $multicurl[$i]);
        }

        $running = null;

        do {
            curl_multi_exec($mh, $running);
            curl_multi_select($mh);
        } while ($running > 0);

        #get content and remove handles
        foreach ($multicurl as $k => $ch) {
            #set return default value
            if (!isset($info[$k]['return'])) {
                $info[$k]['return'] = true;
            }
            #set method default value
            if (!isset($info[$k]['method'])) {
                $info[$k]['method'] = 'GET';
            }
            #set data default value
            if (!isset($info[$k]['data'])) {
                $info[$k]['data'] = null;
            }

            #create response/output
            if ($info[$k]['return']) {
                $response[$k]['request'] = array(
                    'url' => $info[$k]['url'],
                    'method' => $info[$k]['method']
                );

                #set header default value
                if (isset($info[$k]['header'])) {
                    $response[$k]['request']['header'] = $info[$k]['header'];
                }
                #set header default value
                if (isset($info[$k]['auth'])) {
                    $response[$k]['request']['auth'] = $info[$k]['auth'];
                }
                #set useragent default value
                if (isset($info[$k]['useragent'])) {
                    $response[$k]['request']['useragent'] = $info[$k]['useragent'];
                }
                #set curlversion default value
                if (isset($info[$k]['curlversion'])) {
                    $response[$k]['request']['curlversion'] = $info[$k]['curlversion'];
                }
                #set header default value
                if (isset($info[$k]['data']) && ($info[$k]['method'] != 'GET')) {
                    if ($info[$k]['data']) {
                        $response[$k]['request']['data'] = $info[$k]['data'];
                    }
                }
                #set response
                $response[$k]['response'] = curl_multi_getcontent($ch);
            }
            curl_multi_remove_handle($mh, $ch);
        }

        #close
        curl_multi_close($mh);

        #$response_info = curl_getinfo($mh);

        $exetime = microtime(TRUE) - $stm;
        $response = array(
            'status' => 'success',
            'exetime' => $exetime,
            'parenturl' => $GLOBALS['currenturl'] ?? '',
            'tokenid' => $GLOBALS['_tokenid_'] ?? '',
            'usersid' => $GLOBALS['_usersid_'] ?? '',
            'devicetime' => $GLOBALS['_devicetime_'] ?? '',
            'ipaddress' => $GLOBALS['_ipaddress_'] ?? '',
            'deviceid' => $GLOBALS['_deviceid_'] ?? '',
            'data' => (count($response) == 1) ? current($response) : $response
        );

        #@file_put_contents('public/uploads/couponcode/easemytrip.html', '<pre>'.print_r($response, true).'<hr>', FILE_APPEND);
        //*
        if (isset($response['data']['request']['url']) && (strstr($response['data']['request']['url'], 'easemytrip.com'))) {
            //@file_put_contents('public/uploads/couponcode/domestic-roundtrip-easemytrip.html', '<h1>User Id: ' . ($GLOBALS['_usersid_'] ?? '') . '</h1><pre>' . print_r($response, true), FILE_APPEND);
        }
        //*/

        return $response;
    }

    private function _webcontent_old($url, $data = array(), $method = 'get', $headers = array(), $auths = '') {
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            die("Warning: Wrong web url <h4>{$url}</h4>");
        }

        $method = strtoupper($method);

        if (in_array($method, array('GET', 'PUT'))) {
            $url = $url . '?' . http_build_query($data);
        } else if ($method == 'POST') {
            $url = $url;
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_ENCODING, '');
        curl_setopt($ch, CURLOPT_MAXREDIRS, '');
        curl_setopt($ch, CURLOPT_TIMEOUT, 0);
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
        if ($method == 'POST') {
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        }

        if (is_array($headers) && count($headers)) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }

        if (is_string($auths) && strlen(trim($auths))) {
            curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
            curl_setopt($ch, CURLOPT_USERPWD, $auths);
        }

        $response = curl_exec($ch);
        curl_close($ch);
        return $response;
    }

}
