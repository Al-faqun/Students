<?php
//define DOC_ROOT const in this tests folder
define('Shinoa\StudentsList\Tests\DOC_ROOT', dirname(__FILE__, 3));
//set autoloader of classes
include_once('autoload.php');

$pathToConf = \Shinoa\StudentsList\Tests\DOC_ROOT . DIRECTORY_SEPARATOR . 'ini' . DIRECTORY_SEPARATOR . 'config.xml';
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
} else throw new Exception('Path to testsuit config is wrong! Failure to execute');