<?php

class URI {

    public $uri;

    function __construct() {
        $this->_get_request_uri();
    }

    private function _get_request_uri() {
        if (!isset($_SERVER['REQUEST_URI'], $_SERVER['SCRIPT_NAME'])) {
            return FALSE;
        }

        $uri = parse_url('http://nexo' . $_SERVER['REQUEST_URI']);
        $uri = $fullUri = isset($uri['path']) ? $uri['path'] : '';
        $query = isset($uri['query']) ? $uri['query'] : '';

        if (isset($_SERVER['SCRIPT_NAME'][0])) {
            if (strpos($uri, $_SERVER['SCRIPT_NAME']) === 0) {
                $uri = (string) substr($uri, strlen($_SERVER['SCRIPT_NAME']));
            } elseif (strpos($uri, dirname($_SERVER['SCRIPT_NAME'])) === 0) {
                $uri = (string) substr($uri, strlen(dirname($_SERVER['SCRIPT_NAME'])));
            }
        }

        if (trim($uri) === "/") {
            $base_url = $fullUri;
        } else {
            $base_url = str_replace($uri, '', $fullUri);
        }

        $base_url = strip_tags($base_url);

        $ishttps = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') || (isset($_SERVER['SERVER_PORT']) && (int) $_SERVER['SERVER_PORT'] === 443) || (isset($_SERVER['SERVER_PORT']) && (int) $_SERVER['SERVER_PORT'] === 8443);

        $requestscheme = $ishttps ? 'https' : 'http';

        $GLOBALS['base_url'] = $requestscheme . '://' . $_SERVER['HTTP_HOST'] . $base_url;

        $GLOBALS['document_url'] = $_SERVER['DOCUMENT_ROOT'] . $base_url;
        $GLOBALS['request_method'] = $_SERVER['REQUEST_METHOD'];

        if (trim($uri, '/') === '' && strncmp($query, '/', 1) === 0) {
            $query = explode('?', $query, 2);
            $uri = $query[0];
            $_SERVER['QUERY_STRING'] = isset($query[1]) ? $query[1] : '';
        } else {
            $_SERVER['QUERY_STRING'] = $query;
        }

        parse_str($_SERVER['QUERY_STRING'], $_GET);

        if ($uri === '/' OR $uri === '') {
            return '/';
        }
        # now do final cleaning of uri
        $this->uri = $this->_remove_relative_directory($uri);
        #set current url
        $GLOBALS['currenturl'] = $GLOBALS['base_url'] . '/' . $this->uri;
    }

    private function _remove_relative_directory($uri) {
        $uris = array();
        $tok = strtok($uri, '/');
        while ($tok !== FALSE) {
            if ((!empty($tok) OR $tok === '0') && $tok !== '..') {
                $uris[] = $tok;
            }
            $tok = strtok('/');
        }
        return implode('/', $uris);
    }
}
