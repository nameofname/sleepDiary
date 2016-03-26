<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$active_group = 'default';
$query_builder = TRUE;



$db['default'] = array(
	'dsn'	=> '',
    'hostname' => getenv('heroku_db_host'),
    'username' => getenv('heroku_db_user'),
    'password' => getenv('heroku_db_pw'),
    'database' => getenv('heroku_db_db'),
	'dbdriver' => 'mysqli',
	'dbprefix' => '',
	'pconnect' => TRUE,
	'db_debug' => TRUE,
	'cache_on' => FALSE,
	'cachedir' => '',
	'char_set' => 'utf8',
	'dbcollat' => 'utf8_general_ci',
	'swap_pre' => '',
	'autoinit' => TRUE,
	'encrypt' => FALSE,
	'compress' => FALSE,
	'stricton' => FALSE,
	'failover' => array(),
	'save_queries' => TRUE
);

/* End of file database.php */
/* Location: ./application/config/database.php */
