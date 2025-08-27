<?php

if (file_exists(SYSTEMPATH . 'drivers/' . $GLOBALS['DB']['els']['DB_DRIVER'] . '/elsdrivers.php')) {
    require_once(SYSTEMPATH . 'drivers/' . $GLOBALS['DB']['els']['DB_DRIVER'] . '/elsdrivers.php');
}

if (file_exists(SYSTEMPATH . 'drivers/' . $GLOBALS['DB']['els']['DB_DRIVER'] . '/ielsmodels.php')) {
    require_once(SYSTEMPATH . 'drivers/' . $GLOBALS['DB']['els']['DB_DRIVER'] . '/ielsmodels.php');
}

class elsmodels extends elsdrivers implements ielsmodels {

    private $result = array();    #keeps select query result as array or object
    private $sql_command;     #keeps sql command
    public $connection;     #keeps connection link
    private $saved_query;     #keeps all executed query if permitted by database.php in config file
    private $indexname;     #keeps table name
    private $field_value_array = array(); #keeps filed value in array 
    public $insertids = array();   #keeps all inserted ids
    public $insertid;      #keeps last insert id
    private $is_single_dimension = true; #check if array is a single dimention
    private $where;       #keeps where clause
    private $action;      #keeps action name like 'insert', 'update', 'delete', 'select' etc.
    private $update_set_command;    #keeps set command for update query
    private $limit = array();
    private $queryinfo;
    private $having = '';
    private $order_by = '';
    private $group_by = '';
    private $select_fields = array();
    private $join = array();
    private $on = array();
    private $n = 0;
    private $execution_time;
    public $rowaffected;
    private $maincommand;
    private $method = 'GET';
    private $info = array();
    private $data = array('from' => 0, 'size' => 200);
    private $fieldslist = array();
    private $wait = true;
    private $refresh = 'true';

    function __construct() {
        parent::__construct();
        $this->saved_query = array();
    }

    /*
     * Get Elastic Search Information
     */

    public function elsinfo() {
        $info = array('maincommand' => '/', 'method' => 'get');
        $this->_query($info);
        $this->result = $this->_get_data();
        return json_decode($this->result['data']['response']);
    }

    /*
     *
     * create insert query and execute
     *
     */

    public function insert($data_array = array(), $chunk_split = 1) {
        $this->info = array('maincommand' => $this->indexname . '/_bulk?refresh=' . $this->refresh, 'method' => 'POST');

        $this->action = 'insert';
        #if data_array is object then parse it into array
        if (is_object($data_array)) {
            $data_array = (array) $data_array;
        }
        #set field value array
        if (is_array($data_array) && count($data_array)) {
            $firstrow = current($data_array);
            if (is_array($firstrow)) {
                $this->field_value_array = $data_array;
            } else {
                $this->field_value_array = array($data_array);
            }
        }



        $data = array();

        $datajson = array();
        foreach ($this->field_value_array as $rows) {
            $indexjson = array('index' => (object) array());
            $fielddata = array();
            #add field data
            foreach ($rows as $key => $value) {
                $fielddata[$key] = $value;
            }
            #check if fields are not available
            $this->checkiffieldsnotexists($fielddata, $actionname = 'insert');

            if (in_array('timestamp', $this->fieldslist)) {
                ob_start();
                $tsmp = (double) system('date +%s%N');
                ob_end_clean();
                $fielddata['timestamp'] = $tsmp;
            }

            $datajson[] = json_encode($indexjson);
            $datajson[] = json_encode($fielddata);
        }
        $data = implode(PHP_EOL, $datajson) . "\n";
        $this->info['data'] = $data;
        return $this;
    }

    public function nowait() {
        $this->refresh = 'false';
        //$this->wait = false;
        return $this;
    }

    /*
     *
     * return last inserted id
     *
     */

    public function getlastinsertid() {
        return $this->insertid;
    }

    /*
     *
     * return all inserted ids
     *
     */

    public function getallinsertid() {
        return $this->insertids;
    }

    /*
     *
     * set mysql limit
     *
     */

    public function limit($start, $offset = 50) {

        if (!is_numeric($offset)) {
            $offset = 50;
        }

        $this->info['data']['from'] = $start;
        $this->info['data']['size'] = $offset;
        return $this;
    }

    /*
     * Add field selection list
     * params = string OR array()
     */

    public function select($fields = false) {
        $this->action = 'select';
        $this->select_fields = array();
        if ($fields) {
            if (is_array($fields)) {
                $this->select_fields = $fields;
            } else {
                $this->select_fields = explode(',', $fields);
            }
            if (is_array($this->select_fields)) {
                foreach ($this->select_fields as $k => $v) {
                    $this->select_fields[$k] = trim($v);
                }
            }
        }

        #$this->indexname = $this->indexname . '/' . $this->indexname;  #FOR VERSION BEFORE 7+

        $this->info = array('maincommand' => $this->indexname . '/_search', 'data' => $this->data, 'method' => 'POST');

        /*
         * Add field selection list
         */

        if (count($this->select_fields)) {
            $this->info['data']['_source']['includes'] = $this->select_fields;
        }

        return $this;
    }

    /*
     *
     * set index name 
     *
     */

    public function index($indexname) {
        $this->indexname = $indexname;
        return $this;
    }

    /*
     *
     * set index name 
     *
     */

    public function table($indexname) {
        $this->indexname = $indexname;
        return $this;
    }

    /*
     *
     * get list of index (database)
     *
     */

    public function indexlist() {
        $this->info = array('maincommand' => '_cat/indices?format=json', 'method' => 'GET');
        return $this;
    }

    /*
     *
     * get fields list of index (database)
     *
     */

    public function indexfieldlist($fieldinfo = false) {
        $info = array('maincommand' => $this->indexname . '/_mapping/', 'method' => 'GET');
        #get response
        $this->_query($info);
        $res = $this->_get_data();
        $res = json_decode($res['data']['response']);
        $response = array();
        if (isset($res->{$this->indexname}->mappings->properties)) {
            $res = $res->{$this->indexname}->mappings->properties;
            $response = (array) $res;
        };
        if ($fieldinfo) {
            $response = json_decode(json_encode($response));
        } else {
            $response = array_keys($response);
        }
        return $response;
    }

    /*
     *
     * set sql command to query
     *
     */

    public function inlinequery($info = array('cmd' => '', 'data' => array(), 'method' => 'GET')) {
        $this->info = array();
        $this->maincommand = '';
        if (isset($info['cmd'])) {
            $this->info['maincommand'] = $info['cmd'];
        }

        if (isset($info['data'])) {
            $this->info['data'] = is_array($info['data']) ? json_encode($info['data']) : $info['data'];
        }

        if (isset($info['method'])) {
            $this->info['method'] = $info['method'];
        }
        return $this;
    }

    /*
     *
     * Returns executed query result
     *
     */

    public function result($return_type = 'object') {
        $response = array();
        #add exetime
        $response['exetime'] = $this->result['exetime'];
        if (isset($this->result['data']['response'])) {
            $this->result = $this->result['data']['response'];
//            echo '<pre>';
//            print_r($this->result);
//            die;
            if ($this->action == 'select') {
                $this->result = @json_decode($this->_result['data']['response']);
                #add range
                $response['range'] = array('from' => $this->data['from'], 'to' => ($this->data['from'] + $this->data['size']));
                if (isset($this->result->hits)) {
                    #add total records
                    $response['totalrecord'] = $this->result->hits->total ?? 0;
                    #store fields list
                    $response['fields'] = $this->indexfieldlist();
                    #get actual data

                    foreach ($this->result->hits->hits as $k => $row) {


                        #skips fields data
                        if (isset($row->_source->mappings)) {
                            continue;
                        }
                        #everythings is okay, go ahead
                        foreach ($response['fields'] as $field) {
                            if (count($this->select_fields) == 0) {
                                if (!isset($row->_source->{$field})) {
                                    $row->_source->{$field} = null;
                                }
                            }
                        }

                        #add _id
                        $row->_source->_id = $row->_id;
                        #add _score
                        $row->_source->_score = $row->_score;

                        if ($return_type == 'array') {
                            $response['data'][] = (array) $row->_source;
                        } else {
                            $response['data'][] = $row->_source;
                        }
                    }
                }
                return $response;
            } else {
                if ($return_type == 'array') {
                    return @json_decode($this->_result['data']['response'], TRUE);
                }
                return @json_decode($this->_result['data']['response']);
            }
        }
        return false;
    }

    /*
     *
     * Returns executed query result first row only
     *
     */

    public function row($return_type = 'object') {
        if (isset($this->result['data']['response'])) {
            if ($this->action == 'select') {
                $this->result = @json_decode($this->_result['data']['response']);
                #add range
                $response['range'] = array('from' => $this->data['from'], 'to' => ($this->data['from'] + $this->data['size']));
                #add total records
                if (isset($this->result->hits)) {
                    $response['totalrecord'] = $this->result->hits->total;
                    #store fields list
                    $response['fields'] = $this->indexfieldlist();
                    #get actual data
                    foreach ($this->result->hits->hits as $k => $row) {
                        #skips fields data
                        if (isset($row->_source->mappings)) {
                            continue;
                        }
                        #everythings is okay, go ahead
                        foreach ($response['fields'] as $field) {
                            if (count($this->select_fields) == 0) {
                                if (!isset($row->_source->{$field})) {
                                    $row->_source->{$field} = null;
                                }
                            }
                        }
                        #add id
                        $row->_source->_id = $row->_id;
                        #add _score
                        $row->_source->_score = $row->_score;
                        if ($return_type == 'array') {
                            $response['data'] = (array) $row->_source;
                        } else {
                            $response['data'] = $row->_source;
                        }
                        break;
                    }
                }
                return $response;
            } else {
                if ($return_type == 'array') {
                    $this->result = @json_decode($this->_result['data']['response'], TRUE);
                } else {
                    $this->result = @json_decode($this->_result['data']['response']);
                }
            }
        } else {
            $this->result = array();
        }
        return count((array) $this->result) ? current($this->result) : $this->result;
    }

    /*
     *
     * create query command for select, update, delete 
     *
     */

    public function viewquery() {
        $method = isset($this->info['method']) ? $this->info['method'] : $this->method;
        $query = '<b>' . strtoupper($method) . '</b>';
        $query .= ' ';
        $query .= isset($this->info['maincommand']) ? $this->info['maincommand'] : '';
        if (isset($this->info['data'])) {
            $query .= PHP_EOL;
            if (is_array($this->info['data'])) {
                $query .= '<pre>' . json_encode($this->info['data']) . '</pre>';
            } else {
                $query .= '<pre>' . $this->info['data'] . '</pre>';
            }
        }
        /*
         * Now save query if permitted by users
         */
        if ($this->SAVE_QUERIES) {
            $this->saved_query[] = $query;
        }
        #reset value
        $this->resetvalues();

        #return current query
        return $query;
    }

    /*
     *
     * execute mysql command and set last insert id
     *
     */

    public function execute() {
        $this->_query($this->info, $this->wait);
        $this->result = $this->_get_data();

        if (isset($this->result['data']['response']) && $this->action == 'insert') {
            $result = json_decode($this->result['data']['response']);
            if (isset($result->items)) {
                foreach ($result->items as $rows) {
                    if (isset($rows->index->_id)) {
                        #set all insert id
                        $this->insertids[] = $rows->index->_id;
                    }
                }
                #set last insert id
                if (count($this->insertids)) {
                    $this->insertid = end($this->insertids);
                }
            }
        }
        $this->info = array();
        return $this;
    }

    /*
     *
     * return db query results counting
     *
     */

    public function totalrows() {
        return count($this->result());
    }

    /*
     *
     * set join table name
     *
     */

    public function join($table_name) {
        $this->join['J'][$this->n++] = $table_name;
        return $this;
    }

    /*
     *
     * set left join table name
     *
     */

    public function leftjoin($table_name) {
        $this->join['L'][$this->n++] = $table_name;
        return $this;
    }

    /*
     *
     * set right_join table name
     *
     */

    public function rightjoin($table_name) {
        $this->join['R'][$this->n++] = $table_name;
        return $this;
    }

    /*
     *
     * set on clause where condition
     *
     */

    public function on($where_condition) {
        $this->on[] = $this->_where($where_condition, TRUE);
        return $this;
    }

    /*
     *
     * set group by clause 
     *
     */

    public function groupby($field_name) {
        $this->group_by = " GROUP BY `" . str_replace(".", "`.`", $field_name) . "`";
        return $this;
    }

    /*
     *
     * set having clause
     *
     */

    public function having($having_array) {
        $this->having = " HAVING " . $this->_where($having_array);
        return $this;
    }

    /*
     *
     * set order by clause 
     *
     */

    public function orderby($field_name, $order_by = 'asc') {
        $sort = array();
        $sort[$field_name] = array("order" => $order_by);
        #set order value to info
        $this->info['data']['sort'][] = $sort;
//        echo '<pre>';
//        print_r(json_encode($sort));
//        die;

        return $this;
    }

//    "sort" : [
//        { "post_date" : {"order" : "asc"}}
//    ]
    // {"timestamp":{"order":"desc"}}


    private function resetvalues() {
        $this->limit = "";
        $this->having = "";
        $this->order_by = "";
        $this->group_by = "";
        $this->where = "";
        $this->join = array();
        $this->action = "";
        $this->select_fields = "";
        $this->indexname = '';
    }

    /*
     *
     * delete from table
     *
     */

    public function delete() {
        $this->action = 'delete';
        #set main command for update
        #$this->indexname = $this->indexname . '/' . $this->indexname; #FOR VERSION BEFORE 7+
        $this->info['maincommand'] = $this->indexname . '/_delete_by_query?refresh=' . $this->refresh;
        $this->info['method'] = 'POST';
        return $this;
    }

    /*
     *
     * update table field vlaues
     *
     */

    public function update($data_array) {
        $this->action = 'update';
        #set main command for update
        #$this->indexname = $this->indexname . '/' . $this->indexname; #FOR VERSION BEFORE 7+
        $this->info['maincommand'] = $this->indexname . '/_update_by_query?refresh=' . $this->refresh;
        $this->info['method'] = 'POST';
        $this->create_update_set_command($data_array);
        return $this;
    }

    private function checkiffieldsnotexists($data_array, $actionname = 'update') {
        #get field list
        $this->fieldslist = $fieldslist = $this->indexfieldlist();
        #check if field exists
        foreach ($data_array as $key => $value) {
            if (!in_array($key, $fieldslist)) {
                echo '<h2>Following field is not present in index "' . $this->indexname . '"</h2>';
                echo '<br><b>Field Name: </b>' . $key;
                echo '<br><br><b>Error In:</b> <span style="color:red;">' . ucwords(strtolower($actionname)) . ' Query</span>';
                echo '<br><br><b>' . ucwords(strtolower($actionname)) . ' data are</b>';
                echo '<pre>' . print_r($data_array, true) . '</pre>';
                echo '<br><b><span style="color:blue;">Query is</span></b><br><br>';
                echo $this->viewquery();
                die;
            }
        }
    }

    /*
     *
     * create update set command
     *
     */

    private function create_update_set_command($data_array) {
        #check if fields exists
        $this->checkiffieldsnotexists($data_array, $actionname = 'update');

        #####everythis is okay now####
        #initiate source variable
        $source = array();

        #create source variable value
        foreach ($data_array as $key => $value) {
            $source[] = "ctx._source." . $key . '=params.' . $key;
        }
        if (count($source)) {
            $source = implode(';', $source);
        }
        #now create params elements key and value for update field
        $params = $data_array;

        #now create scripts array
        $scripts = array(
            'source' => $source,
            'lang' => 'painless',
            'params' => $params
        );



        #now set script values in $this->info
        $this->info['data']['script'] = $scripts;
    }

    /*
     *
     * Returns true/false if table truncated
     *
     */

    public function truncate() {
//        if (!$this->check_if_table_exists($this->table_name)) {
//            return "Table name `" . $this->table_name . "` not found in database `" . $this->DATABASE . "`";
//        }
        $this->action = 'truncate';
        $this->info['method'] = 'POST';
        $this->info['maincommand'] = $this->indexname . '/_delete_by_query?conflicts=proceed&refresh=' . $this->refresh;
        $this->info['data']['query'] = array('match_all' => (object) array());
        return $this;
    }

    /*
     *
     * set where caluse
     *
     */

    public function where($where = array()) {
        $this->where = false;
        if (is_array($where) && count($where)) {
            $this->where = ($this->_where($where));
            if ($this->where) {
                $this->info['data']['query'] = $this->where['query'];
            }
        } else {
            if (!empty($where)) {
                $this->where = $where;
                $this->info['data']['query'] = json_decode($this->where);
            }
        }
        return $this;
    }

    private function _where($where, $nested = false, $return = array(), $operator = false, $condition = false) {#recursion method which manage where condition
        if (!empty($where)) {
            $return = array();

            if (is_array($where) && is_array(current($where))) {
                foreach ($where as $row) {
                    $return["bool"] = $this->_where($row, $nested = 1, $return = array(), $operator = false, $condition = false);
                }
            } else {
                if (is_array($where) && count($where) == 3) {
                    $comparator = $this->getcomparatorword($where[1]);
                    if ($nested) {
                        $farr = array();
                        $farr[$comparator][current($where)] = end($where);
                        $return['must'][] = $farr;
                    } else {
                        $return['query'][$comparator][current($where)] = end($where);
                    }
                } else {
                    #manage here condition like 'AND', 'OR', 'LIKE', 'NOT'
                    if ($where == 'and') {

                        $return['must']['match_phrase'][current($where)] = end($where);
                    }
                }
            }



            /* foreach ($where as $key => $value) {


              if (is_array($value)) {
              if (count($value)) {
              $return = $return . "(" . $this->_where($value, $return = array(), $operator = false, $condition = false) . ")";
              }
              } else {
              if (strtolower($value) == 'and' || strtolower($value) == 'or' || strtolower($value) == 'like') {
              $return .= " " . strtoupper($value) . " ";
              } else {
              if ($key == 0) { #following is field name
              $return .= "`" . str_replace(".", "`.`", $value) . "` ";
              } else if ($key == 2) { #following is field value
              $return .= " '" . $value . "' ";
              } else { #following is comparator
              $return .= $value;
              }
              }
              }



              } */
            return $return;
        } else {
            return $where;
        }
    }

    /*
      MYSQL
      ----------------
      where ((title='Elasticsearch' OR title='Solr') AND (authors='one')) AND (authors!='two')


      ElasticSearch
      ----------------
      {
      "query": {
      "bool": {
      "must": {
      "bool" : {
      "should": [
      { "match": { "title": "Elasticsearch" }},
      { "match": { "title": "Solr" }}
      ],
      "must": { "match": { "authors": "one" }}
      }
      },
      "must_not": { "match": {"authors": "two" }}
      }
      }
      }
     */

    /*
      {
      "query": {
      "must":[
      {"term": {"name":"abhay"}},
      {"term": {"name":"abhay"}}
      ]
      }
      }
     */

    private function justold_where($where, $return = array(), $operator = false, $condition = false) {#recursion method which manage where condition
        if (!empty($where) && is_array($where)) {
            $row = array();
            $symbol = '=';

            foreach ($where as $key => $value) {
                if (is_array($value)) {
                    if (count($value)) {
                        $return = $this->_where($value, $return, $operator, $condition);
                    }
                } else {
                    if (strtolower($value) == 'and' || strtolower($value) == 'or' || strtolower($value) == 'like') {
                        $condition = 'must';
                        $arr = array();
                        $arr[$condition] = $return;

                        $return = $arr;
                    } else {
                        if ($key == 0) { #following is field name
                            continue;
                        } else if ($key == 2) { #following is field value
                            $comparatorword = $this->getcomparatorword($operator);
                            $arr = array();
                            if ($condition) {
                                //echo $condition; die;
                                $arr = array($comparatorword => array($where[$key - 2] => $value));
                                $return[$condition] = $arr;

                                //$operator = false;
                                $condition = false;
                                //$result[$condition][$operator][$comparatorword][$key - 2][] = $value;
                            } else {
                                $arr = array($comparatorword => array($where[$key - 2] => $value));
                                $return = $arr;
                            }
                            //$return[$comparatorword][$where[$key - 2]] = $value;
                        } else { #following is comparator
                            $operator = $value;
                        }
                    }
                }
            }
            return $return;
        } else {
            return $where;
        }
    }

    private function getcomparatorword($symbol) {
        $word = 'term';
        switch ($symbol) {
            case '=': return 'match_phrase';
                break;
        }
        return $word;
    }

    //{"term": {"color": "red"}},


    private function xxx_where($where) {#recursion method which manage where condition
        if (!empty($where) && is_array($where)) {
            $return = "";
            foreach ($where as $key => $value) {
                if (is_array($value)) {
                    if (count($value)) {
                        $return = $return . "(" . $this->_where($value, $on) . ")";
                    }
                } else {
                    if (strtolower($value) == 'and' || strtolower($value) == 'or' || strtolower($value) == 'like') {
                        $return .= " " . strtoupper($value) . " ";
                    } else {
                        if ($key == 0) { #following is field name
                            $return .= "`" . str_replace(".", "`.`", $value) . "` ";
                        } else if ($key == 2) { #following is field value
                            $return .= " '" . $value . "' ";
                        } else { #following is comparator
                            $return .= $value;
                        }
                    }
                }
            }
            return $return;
        } else {
            return $where;
        }
    }

    public function rowaffected() {
        if ($this->action == 'update') {
            $res = @json_decode($this->result['data']['response']);
            if (isset($res->updated) && $res->updated > 0) {
                return true;
            }
            return false;
        } else if ($this->action == 'delete') {
            $res = @json_decode($this->result['data']['response']);
            if (isset($res->deleted) && $res->deleted > 0) {
                return true;
            }
            return false;
        }
        return false;
    }

    /*
     *
     * check if array is associative or indexed
     *
     */

    private function is_assoc(array $arr) {
        if (array() === $arr)
            return false;
        return array_keys($arr) !== range(0, count($arr) - 1);
    }

    /*
     *
     * Returns store procedure query result after execution
     *
     */

    public function storeprocedure($sp_name, $params = array()) {
        $str = array();
        if (!is_array($params)) {
            $params = explode(",", str_replace("'", "", $params));
        }
        #remove extra spaces in values
        array_walk($params, function(&$value) {
            $value = "'" . trim($value) . "'";
        });

        $str = implode(",", $params);

        $this->sql_command = "CALL " . $sp_name . " (" . $str . ");";
        return $this;
    }

    /*
     *
     * Returns table's fields list of connected database
     *
     */

    public function gettablefieldslist($table_name) {
        if (!$this->check_if_table_exists($table_name)) {
            return "Table name `" . $table_name . "` not found in database `" . $this->DATABASE . "`";
        }
        $this->sql_command = "SHOW COLUMNS FROM `" . $table_name . "` FROM `" . $this->DATABASE . "`";
        return $this->execute()->result();
    }

    /*
     *
     * Returns true/false if table exists in database
     *
     */

    private function check_if_table_exists($table_name) {  #no
        $flag = FALSE;
        foreach ($this->getdatabasetableslist() as $db_table_name) {
            if (strtolower(trim(current($db_table_name))) === strtolower(trim($table_name))) {
                $flag = TRUE;
                break;
            }
        }
        return $flag;
    }

    /*
     *
     * Returns tables list of connected database
     *
     */

    public function getdatabasetableslist() {
        $this->sql_command = "SHOW TABLES FROM `" . $this->DATABASE . "`";
        return $this->execute()->result();
    }

    /*
     *
     * Returns last query executed
     *
     */

    public function getlastquery() {
        $query = end($this->saved_query);
        if (is_array($query)) {
            $query = end($query);
        }
        return $query;
    }

    /*
     *
     * Returns all queries executed
     *
     */

    public function getallquery() {
        return $this->saved_query;
    }

    /*
     *
     * Returns connection info of database connection created
     *
     */

    public function connection() {
        return $this->CONNECTION;
    }

}
