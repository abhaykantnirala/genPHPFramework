<?php

/*
  | -------------------------------------------------------------------
  | DATABASE CONNECTIVITY SETTINGS
  | -------------------------------------------------------------------
  | For complete instructions please consult the 'Database Connection'
  | page of the Framework.
  |
  |
 */

$DB['default'] = array(
    array(
        'QUERY_BUILDER' => TRUE,
        'DNS' => '', //pending
        'HOSTNAME' => 'localhost',
        'USERNAME' => 'root',
        'PASSWORD' => '12345',
        'DATABASE' => 'nexo',
        'DB_DRIVER' => 'mysqli', //supported driver => (mysqli, mysqliPDO)
        'DB_PREFIX' => '',
        'PCONNECT' => FALSE, //pending
        'DB_DEBUG' => TRUE,
        'CACHE_ON' => FALSE, //pending
        'CACHE_DIR' => '', //pending
        'CHAR_SET' => 'utf8',
        'DBCOLLAT' => 'utf8_general_ci',
        'SWAP_PRE' => '', //pending
        'ENCRYPT' => FALSE, //pending
        'COMPRESS' => FALSE, //pending
        'STRICT_ON' => FALSE,
        'FAILOVER' => array(), //pending
        'SAVE_QUERIES' => TRUE
    )
);

$DB['elsmyadmin'] = array(
    'els' => array(
        'DNS' => '', #pending
        'PORT' => '9243',
        'HOSTNAME'=>'127.0.0.1',
        'USERNAME' => 'elastic',
        'PASSWORD' => 'H6rJWwdMviarw8WQVv2',
        'DATABASE' => 'og0mob',
        'DB_DRIVER' => 'els', #supported driver => (mysqli, mysqliPDO, els)
        'DB_PREFIX' => '',
        'PCONNECT' => FALSE, #pending
        'DB_DEBUG' => TRUE,
        'CACHE_ON' => FALSE, #pending
        'CACHE_DIR' => '', #pending
        'CHAR_SET' => 'utf8',
        'DBCOLLAT' => 'utf8_general_ci',
        'SWAP_PRE' => '', #pending
        'ENCRYPT' => FALSE, #pending
        'COMPRESS' => FALSE, #pending
        'STRICT_ON' => FALSE,
        'FAILOVER' => array(), #pending
        'SAVE_QUERIES' => TRUE,
        'QUERY_BUILDER' => TRUE,
    )
);

