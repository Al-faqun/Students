<?php
//define DOC_ROOT const in this tests folder
include_once('root.php');
//set autoloader of classes
include_once('autoload.php');

$pathToConf = \Shinoa\StudentsList\Tests\DOC_ROOT . DIRECTORY_SEPARATOR . 'Students'
	                . DIRECTORY_SEPARATOR . 'ini' . DIRECTORY_SEPARATOR . 'config_test.xml';
if ( file_exists($pathToConf) ) {
	$test_conf = simplexml_load_file($pathToConf);
	$test_dsn = "mysql:host=localhost;dbname={$test_conf->database->dbname};charset=utf8";
	$test_username = $test_conf->database->username;
	$tets_password = $test_conf->database->password;
	$test_opt = array(
		\PDO::ATTR_ERRMODE            => \PDO::ERRMODE_EXCEPTION,
		\PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
		\PDO::ATTR_EMULATE_PREPARES   => false,
		\PDO::MYSQL_ATTR_FOUND_ROWS   => true
	);
	$test_pdo = new \PDO($test_dsn, $test_username, $tets_password, $test_opt);
} else throw new Exception('Path to config is wrong! Failure to execute');