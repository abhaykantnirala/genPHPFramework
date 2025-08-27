<?php

/**
 *
 */

interface imysqlimodels {

    public function insert($data_array = array(), $chunk_split = 1);   //put $chunk_split = 0 for unlimited queries string

    public function update($data_array);

    public function delete();

    public function truncate();

    public function select($fields);

    public function execute();

    public function inlinequery($sql_command);

    public function viewquery();

    public function storeprocedure($sp_name, $params = array());

    public function table($table_name);

    public function gettablefieldslist($table_name);

    public function getdatabasetableslist();

    public function getlastinsertid();

    public function getallinsertid();

    public function getlastquery();

    public function getallquery();

    public function connection();

    public function where($where);

    public function groupby($field_name);

    public function having($having_array);

    public function orderby($field_name, $order_by = 'ASC');

    public function limit($start, $offset);

    public function join($table_name);

    public function leftjoin($table_name);

    public function rightjoin($table_name);

    public function on($where_condition);

    public function executiontime();

    public function totalrows();

    public function row($return_type = 'object');

    public function result($return_type = 'object');

    public function rowaffected();
}
