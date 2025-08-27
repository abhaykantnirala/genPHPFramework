<?php

namespace model;

use gmodel;

class els extends gmodel {

    function __construct() {
        parent::__construct();
    }

    public function saveindexinsertviewdata($indexname, $dataarr) {
        $this->db->els->index($indexname)->insert($dataarr)->execute()->getlastinsertid();
        return $this->db->els->index($indexname)->insert($dataarr)->viewquery();
    }

    public function getelsinfo() {
        return $this->db->els->elsinfo();
    }

    public function truncateindex($indexname) {
        return $this->db->els->index($indexname)->truncate()->execute()->result();
    }

    public function createindexstructure($indexname, $data) {
        $infodata = array();
        foreach ($data as $rows) {
            $format = 0;
            if ($rows->type == 'date') {
                $format = 1;
            }
            $nullvalue = false;
            $format = false;
            switch ($rows->type) {
                case 'integer': $nullvalue = (integer) $rows->default;
                    break;
                case 'boolean': $nullvalue = (boolean) $rows->default;
                    break;
                case 'date': $nullvalue = $rows->default;
                    $format = "yyyy-MM-dd HH:mm:ss";  #yyyy-MM-dd HH:mm:ss||yyyy-MM-dd||epoch_millis";
                    break;
                case 'double': $nullvalue = (double) $rows->default;
                    break;
                case 'float': $nullvalue = (float) $rows->default;
                    break;
            }
            $info = array();
            $info['type'] = $rows->type;
            if ($nullvalue) {
                $info['null_value'] = $nullvalue;
            }
            if ($format) {
                $info['format'] = $format;
            }

            $infodata[$rows->name] = $info;
        }
        if (count($infodata) == 0) {
            return false;
        }
        $info = array(
            'properties' => $infodata
        );

        #now create structure
        $info = array('cmd' => $indexname . '/_mapping', 'method' => 'PUT', 'data' => $info);
        return $this->db->els->inlinequery($info)->execute()->result();
    }

    public function deleteindextypedoc($indexname, $_id) {
        $info = array('cmd' => $indexname . '/' . $_id, 'method' => 'delete');
        return $this->db->els->inlinequery($info)->execute()->result();
    }

    public function getindextotalrecord($indexname) {
        $info = array('cmd' => $indexname . '/_count', 'method' => 'GET');
        $res = $this->db->els->inlinequery($info)->execute()->result();
        return $res->count ?? 0;
    }

    public function getindextypedata($indexname, $sortinfo = array()) {
        if (is_array($sortinfo) && isset($sortinfo[0]) && trim($sortinfo[0]) != '') {
            $fieldname = current($sortinfo);
            $sortby = end($sortinfo);
            return $this->db->els->index($indexname)->select()->orderby($fieldname, $sortby)->execute()->result();
        }
        return $this->db->els->index($indexname)->select()->execute()->result();
    }

    public function getindexfieldlist($indexname, $mapping = false) {
        $info = array('cmd' => $indexname . '/_mapping/', 'method' => 'GET');
        $res = $this->db->els->inlinequery($info)->execute()->result();
        $response = array();
        if (isset($res->{$indexname}->mappings->properties)) {
            $res = $res->{$indexname}->mappings->properties;
            $response = $res;
        };
        return $response;
    }

    public function getindexlist() {
        $res = $this->db->els->indexlist()->execute()->result();
        if (is_array($res)) {
            foreach ($res as $key => $row) {
                $res[$row->index] = $row;
                unset($res[$key]);
            }
            ksort($res);
        } else {
            $res = array();
        }
        return $res;
    }

    public function createindex($indexname, $numberofshards, $numberofreplicas) {
        $data = array(
            'settings' => array(
                'number_of_shards' => $numberofshards,
                'number_of_replicas' => $numberofreplicas,
            )
        );
        $info = array('cmd' => $indexname, 'method' => 'put', 'data' => json_encode($data));
        return $this->db->els->inlinequery($info)->execute()->result();
    }

    public function deleteindex($indexname) {
        $info = array('cmd' => $indexname, 'method' => 'delete');
        return $this->db->els->inlinequery($info)->execute()->result();
    }

}
