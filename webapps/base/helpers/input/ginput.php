<?php

class input implements iginput {

    public $data;
    private $requesttype = 'GET';

    function __construct() {
        // Secure initialization with input validation
        $requestUri = isset($GLOBALS['_SERVER']['REQUEST_URI']) ? $GLOBALS['_SERVER']['REQUEST_URI'] : '';
        $requestMethod = isset($GLOBALS['request_method']) ? $GLOBALS['request_method'] : 'GET';
        
        // Sanitize request URI
        $requestUri = NexoSecurity::sanitize($requestUri, 'url');
        
        $this->data = explode("?", $requestUri);
        $this->data = isset($this->data[1]) ? NexoSecurity::sanitize($this->data[1], 'string') : '';
        $this->requesttype = NexoSecurity::sanitize($requestMethod, 'alpha');
    }

    /**
     * Get and sanitize HTTP headers
     * 
     * @param string $key Specific header to retrieve
     * @return mixed Sanitized header data
     */
    public function header($key = '') {
        $headers = getallheaders();
        if (trim($key)) {
            $key = NexoSecurity::sanitize($key, 'string');
            $data = isset($headers[$key]) ? $headers[$key] : '';
            
            // Sanitize header value
            $data = NexoSecurity::sanitize($data, 'string');
        } else {
            $data = isset($headers) ? $headers : array();
            
            // Sanitize all headers
            if (is_array($data)) {
                $sanitizedHeaders = array();
                foreach ($data as $k => $v) {
                    $sanitizedKey = NexoSecurity::sanitize($k, 'string');
                    $sanitizedValue = NexoSecurity::sanitize($v, 'string');
                    $sanitizedHeaders[$sanitizedKey] = $sanitizedValue;
                }
                $data = $sanitizedHeaders;
            }
        }
        return $data;
    }

    /**
     * Get and sanitize GET parameters
     * 
     * @param string $key Specific key to retrieve
     * @return mixed Sanitized GET data
     */
    public function get($key = '') {
        $data = array();
        $get = array();

        if (count($_GET)) {
            // Sanitize $_GET data
            foreach ($_GET as $k => $v) {
                $sanitizedKey = NexoSecurity::sanitize($k, 'string');
                $sanitizedValue = NexoSecurity::sanitize($v, 'string');
                $data[$sanitizedKey] = $sanitizedValue;
            }
        } else {
            if (strstr($this->data, "&")) {
                $data = explode("&", $this->data);
            } else {
                $data[] = $this->data;
            }
        }

        foreach ($data as $value) {
            if (is_array($value)) {
                continue; // Skip already processed data
            }
            $value = explode("=", $value);
            if (current($value)) {
                $key_name = NexoSecurity::sanitize(current($value), 'string');
                $key_value = NexoSecurity::sanitize(end($value), 'string');
                
                // Additional validation
                list($validatedValue, $isValid, $errors) = NexoSecurity::validateInput($key_value, [
                    'type' => 'string',
                    'length' => ['max' => 2000]
                ]);
                
                $get[$key_name] = $validatedValue;
            }
        }

        if (trim($key)) {
            $key = NexoSecurity::sanitize($key, 'string');
            $get = isset($get[$key]) ? $get[$key] : '';
        }
        return $get;
    }

    /**
     * Get and sanitize POST parameters
     * 
     * @param string $key Specific key to retrieve
     * @return mixed Sanitized POST data
     */
    public function post($key = '') {
        if (trim($key)) {
            $key = NexoSecurity::sanitize($key, 'string');
            $data = isset($_POST[$key]) ? $_POST[$key] : '';
            
            // Sanitize POST value
            $data = NexoSecurity::sanitize($data, 'string');
            
            // Additional validation for POST data
            if (is_string($data)) {
                list($validatedData, $isValid, $errors) = NexoSecurity::validateInput($data, [
                    'type' => 'string',
                    'length' => ['max' => 10000] // Allow larger POST data
                ]);
                $data = $validatedData;
            }
        } else {
            $data = isset($_POST) ? $_POST : array();
            
            // Sanitize entire POST array
            if (is_array($data)) {
                $sanitizedData = array();
                foreach ($data as $k => $v) {
                    $sanitizedKey = NexoSecurity::sanitize($k, 'string');
                    $sanitizedValue = NexoSecurity::sanitize($v, 'string');
                    $sanitizedData[$sanitizedKey] = $sanitizedValue;
                }
                $data = $sanitizedData;
            }
        }
        return $data;
    }

    /**
     * Get and sanitize PUT/PATCH data
     * 
     * @return string Sanitized PUT data
     */
    public function put() {
        $data = file_get_contents("php://input");
        
        // Sanitize PUT data
        $data = NexoSecurity::sanitize($data, 'string');
        
        // Validate PUT data
        list($validatedData, $isValid, $errors) = NexoSecurity::validateInput($data, [
            'type' => 'string',
            'length' => ['max' => 50000] // Reasonable limit for PUT data
        ]);
        
        return $validatedData;
    }

    /**
     * Get and validate file uploads
     * 
     * @param string $key Specific file input name
     * @return mixed Validated file data or false
     */
    public function file($key = '') {
        if (isset($_FILES)) {
            $file = $_FILES;
            if (!empty($key)) {
                $key = NexoSecurity::sanitize($key, 'string');
                if (isset($_FILES[$key])) {
                    $file = $_FILES[$key];
                    
                    // Validate filename for security
                    if (isset($file['name'])) {
                        $file['name'] = NexoSecurity::sanitize($file['name'], 'filename');
                        
                        // Additional file security checks
                        if (NexoSecurity::detectPathTraversal($file['name'])) {
                            NexoSecurity::logSecurityViolation('FILE_PATH_TRAVERSAL', $file['name']);
                            return FALSE;
                        }
                    }
                } else {
                    return FALSE;
                }
            } else {
                // Sanitize all file upload data
                $sanitizedFiles = array();
                foreach ($file as $inputName => $fileData) {
                    $sanitizedInputName = NexoSecurity::sanitize($inputName, 'string');
                    if (is_array($fileData) && isset($fileData['name'])) {
                        $fileData['name'] = NexoSecurity::sanitize($fileData['name'], 'filename');
                        
                        // Security check for path traversal
                        if (NexoSecurity::detectPathTraversal($fileData['name'])) {
                            NexoSecurity::logSecurityViolation('FILE_PATH_TRAVERSAL', $fileData['name']);
                            continue; // Skip malicious file
                        }
                    }
                    $sanitizedFiles[$sanitizedInputName] = $fileData;
                }
                $file = $sanitizedFiles;
            }
            return $file;
        }
        return FALSE;
    }
}
