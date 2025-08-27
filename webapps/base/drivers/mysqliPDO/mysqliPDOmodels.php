<?php

if (file_exists(SYSTEMPATH . 'drivers/' . $GLOBALS['DB']['mysqliPDO']['DB_DRIVER'] . '/mysqliPDOdrivers.php')) {
    require_once(SYSTEMPATH . 'drivers/' . $GLOBALS['DB']['mysqliPDO']['DB_DRIVER'] . '/mysqliPDOdrivers.php');
}

if (file_exists(SYSTEMPATH . 'drivers/' . $GLOBALS['DB']['mysqliPDO']['DB_DRIVER'] . '/imysqliPDOmodels.php')) {
    require_once(SYSTEMPATH . 'drivers/' . $GLOBALS['DB']['mysqliPDO']['DB_DRIVER'] . '/imysqliPDOmodels.php');
}

class mysqliPDOmodels extends mysqliPDOdrivers implements imysqliPDOmodels {

    private $result = array();    //keeps select query result as array or object
    private $sql_command;     //keeps sql command
    public $connection;     //keeps connection link
    private $saved_query;     //keeps all executed query if permitted by database.php in config file
    private $table_name;     //keeps table name
    private $field_value_array = array(); //keeps filed value in array 
    public $insertids = array();   //keeps all inserted ids
    public $insertid;      //keeps last insert id
    private $is_single_dimension = true; //check if array is a single dimention
    private $where;       //keeps where clause
    private $action;      //keeps action name like 'insert', 'update', 'delete', 'select' etc.
    private $update_set_command;    //keeps set command for update query
    private $limit = '';
    private $having = '';
    private $order_by = '';
    private $group_by = '';
    private $select_fields;
    private $join = array();
    private $on = array();
    private $n = 0;
    private $execution_time;
    public $rowaffected;

    function __construct() {
        parent::__construct();
        $this->saved_query = array();
        $this->connection = $this->CONNECTION;
    }
    
    public function select($fields = "*") {
        $this->action = 'select';
        $this->select_fields = $fields;
        return $this;
    }

    public function executiontime() {
        return $this->execution_time;
    }

    /*
     *
     * return db query results counting
     *
     */

    public function totalrows() {
        return count($this->result());
    }

    public function row($type = 'object') {
        if (strtolower($type) == 'array') {
            //return array();
            return $this->_get_assoc_data(1);
            //return $this->_dquery->fetch(2);
        } else {
            //return array();
            return $this->_get_object_data(1);
            //return $this->_dquery->fetch(5);
        }
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

    public function orderby($field_name, $order_by = 'ASC') {
        if (strstr($field_name, "()") && !strstr($field_name, ".")) {
            $this->order_by = " ORDER BY " . $field_name . " " . $order_by;
        } else {
            $this->order_by = " ORDER BY `" . str_replace(".", "`.`", $field_name) . "` " . $order_by;
        }
        return $this;
    }

    /*
     *
     * set mysql limit
     *
     */

    public function limit($start, $offset = FALSE) {
        if ($offset == FALSE) {
            $this->limit = " LIMIT " . $start . " ";
        } else {
            $this->limit = " LIMIT " . $start . ", " . $offset . " ";
        }
        return $this;
    }

    /*
     *
     * create query command for select, update, delete 
     *
     */

    public function viewquery() {
        if ($this->action === 'truncate') {
            $this->sql_command = "TRUNCATE `" . $this->DATABASE . "`.`" . $this->table_name . "`";
        } else if ($this->action === 'delete') {
            $this->sql_command = "DELETE FROM `" . $this->table_name . "` " . $this->where;
        } else if ($this->action === 'update') {
            $this->sql_command = "UPDATE `" . $this->table_name . "` SET " . $this->update_set_command . $this->where;
        } else if ($this->action === 'select') {
            //manage join query first (inner join/join, outer left join/left join, outer right join/right join)
            $joins = "";
            foreach ($this->join as $key => $rows) {
                if ($key === 'J') {
                    $key = 'JOIN';
                } else if ($key === 'L') {
                    $key = 'LEFT JOIN';
                } else if ($key === 'R') {
                    $key = 'RIGHT JOIN';
                }

                foreach ($rows as $k => $value) {
                    $joins .= chr(10) . $key . " `" . $value . "` ";
                    if (isset($this->on[$k])) {
                        $joins .= chr(10) . " ON ";
                        $joins .= chr(10) . $this->on[$k];
                    }
                }
            }

            $this->sql_command = "SELECT " . $this->select_fields . " FROM `" . $this->table_name . "` " . $joins . chr(10) . $this->where . chr(10) . $this->having . ' ' . $this->group_by . chr(10) . $this->order_by . chr(10) . $this->limit . ";";
        } else if ($this->action === 'insert') {
            $this->sql_command = $this->sql_command;
        }
        //now reset all value for later use so that no other query messed up
        $this->resetvalues();
        //return sql command
        return $this->sql_command;
    }

    private function resetvalues() {
        $this->limit = "";
        $this->having = "";
        $this->order_by = "";
        $this->group_by = "";
        $this->where = "";
        $this->join = array();
        $this->action = "";
        $this->select_fields = "";
    }

    /*
     *
     * set sql command to query
     *
     */

    public function inlinequery($sql_command) {
        $this->sql_command = $sql_command;
        return $this;
    }

    /*
     *
     * update table field vlaues
     *
     */

    public function update($data_array) {
        $this->action = 'update';
        $this->update_set_command = $this->create_update_set_command($data_array);
        return $this;
    }

    /*
     *
     * create update set command
     *
     */

    private function create_update_set_command($data_array) {
        if (!$this->is_assoc($data_array)) {
            echo 'Invalid update data array <br><pre>' .
            print_r($data_array);
            echo '</pre>';
            exit(3);
        }
        $str = [];

        foreach ($data_array as $key => $value) {
            $str[] = "`" . $key . "` = '" . $value . "'";
        }
        return implode(",", $str);
    }

    /*
     *
     * Returns true/false if table truncated
     *
     */

    public function truncate() {
        if (!$this->check_if_table_exists($this->table_name)) {
            return "Table name `" . $this->table_name . "` not found in database `" . $this->DATABASE . "`";
        }
        $this->action = 'truncate';
        return $this;
    }

    /*
     *
     * delete from table
     *
     */

    public function delete() {
        $this->action = 'delete';
        return $this;
    }

    /*
     *
     * set where caluse
     *
     */

    public function where($where) {
        $this->where = " WHERE " . $this->_where($where);
        return $this;
    }

    /*
     *
     * return where caluse string
     *
     */

    private function _where($where, $on = FALSE) {#recursion method which manage where condition
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
                        
                        if ($on) {
                            $arr = explode(".", $value);
                            $is_value = count($arr) == 1 ? TRUE : FALSE;
                        } else {
                            $is_value = TRUE;
                        }

                        if ($key == 0) { #following is field name
                            $return .= "`" . str_replace(".", "`.`", $value) . "` ";
                        } else if ($key == 2) { //following is field value
                            if ($is_value) {
                                $return .= " '" . $value . "' ";
                            } else {
                                $return .= " `" . str_replace(".", "`.`", $value) . "` ";
                            }
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
     * create insert query and execute
     *
     */

    public function insert($data_array = array(), $chunk_split = 1) {
        $this->action = 'insert';
        //set field value array
        if (is_array($data_array) && count($data_array)) {
            $this->field_value_array = $data_array;
        }
        //create insert query
        $this->createquery($chunk_split);
        return $this;
    }

    /*
     *
     * create query
     *
     */

    private function createquery($chunk_split = 1) {
        switch ($this->action) {
            case 'insert': $this->createinsertquery($chunk_split);
                break;
        }
    }

    /*
     *
     * create insert query
     *
     */

    private function createinsertquery($chunk_split = 1) {
        if ($this->is_assoc($this->field_value_array)) {
            $this->is_single_dimension = true;  //it means single dimension array
        } else {
            $this->is_single_dimension = false;
        }

        if ($this->is_single_dimension == true) {
            $fields = "`" . implode("`,`", array_keys($this->field_value_array)) . "`";
            $values = "'" . implode("','", array_values($this->field_value_array)) . "'";
            $this->sql_command = "INSERT INTO `" . $this->table_name . "` (" . $fields . ") VALUES(" . $values . ");";
        } else {
            $data_array = $this->field_value_array;
            $queries = "";
            $this->sql_command = array();
            foreach ($data_array as $rows) {
                $this->field_value_array = $rows;
                $fields = "`" . implode("`,`", array_keys($this->field_value_array)) . "`";
                $values = "'" . implode("','", array_values($this->field_value_array)) . "'";
                $sql = "INSERT INTO `" . $this->table_name . "` (" . $fields . ") VALUES(" . $values . ");";
                $this->sql_command[] = $sql;
                $queries .= $sql;
            }

            if (is_numeric($chunk_split) && $chunk_split > 0) {
                $queries = array_chunk($this->sql_command, $chunk_split);
            }
            $this->sql_command = $queries;
        }
    }

    /*
     *
     * execute mysql command and set last insert id
     *
     */

    public function execute() {
        $this->viewquery();

        $sql_command = $this->sql_command;

        /*
         * Now save query if permitted by users
         */
        if ($this->SAVE_QUERIES) {
            $this->saved_query[] = $sql_command;
        }

        // check execution time
        $msc = microtime(true);
        $this->insertids = array();
        /*
         * Execute Query Command
         */
        if (is_array($this->sql_command)) {
            foreach ($this->sql_command as $sql_command) {
                if (is_array($sql_command)) {
                    $sql_command = current($sql_command);
                }
                $this->_query($sql_command);
                //set insert ids
                if (in_array($this->action, array('insert'))) {
                    $this->insertids[] = $this->insertid = $this->_get_insert_id();
                } else {
                    $this->insertids[] = $this->insertid = $this->_get_insert_id();
                }
            }
        } else {
            $this->_query($sql_command);
            //set insert ids
            if (in_array($this->action, array('insert'))) {
                $this->insertids[] = $this->insertid = $this->_get_insert_id();
            } else {
                $this->insertids[] = $this->insertid = $this->_get_insert_id();
            }
        }
        $msc = round((microtime(true) - $msc), 6);
        $this->execution_time = $msc . ' ' . ($msc > 1 ? 'seconds' : 'second');
        $this->rowaffected = $this->_rowaffected(); // $this->_dquery->rowCount();
        return $this;
    }

    public function rowaffected() {
        return $this->rowaffected;
    }

    /*
     *
     * set table name 
     *
     */

    public function table($table_name) {
        $this->table_name = $table_name;
        return $this;
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
     * Returns executed query result
     *
     */

    public function result($return_type = 'object') {
        $this->result = array();
        if (strtolower(trim($return_type)) === 'array') {
            $this->result = $this->_get_assoc_data();
        } else {
            $this->result = $this->_get_object_data();
        }
        return $this->result;
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

    private function check_if_table_exists($table_name) {  //no
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
