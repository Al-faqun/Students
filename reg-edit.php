<?php
	include_once 'bootstrap.php';

	use \Shinoa\StudentsList\Registry;
	use \Shinoa\StudentsList\StudentMapper;
	use \Shinoa\StudentsList\RegEditView;
	use \Shinoa\StudentsList\LoginManager;
	use \Shinoa\StudentsList\PasswordMapper;
	
	$root = dirname(__DIR__);
	$registry = Registry::getInstance();
	$registry->init($root, $root . '/Students/ini/config_test.xml');
	$dataMapper = new StudentMapper( $registry->getPDO() );
	$loginMan   = new LoginManager(new PasswordMapper( $registry->getPDO() ), $_COOKIE );
	$view       = new RegEditView( $registry, $registry->getRoot() . '/Students/templates' );
	$registry->setDataMapper($dataMapper);
	$registry->setLoginManager($loginMan);
	$registry->setView($view);

	$validator = new \Shinoa\StudentsList\StudentValidator($_POST);
	$student = $validator->checkInput($_POST, $errors);
	$loginMan->checkAuth();
	
	if ( $loginMan->isLogged() === false) {
		if (empty($errors)) {
			$dataMapper->insertStudent($student);
			$insertedID = $dataMapper->lastInsertedId();
			$loginMan->logIn($insertedID);
			//редирект
			header('Location: /', true);
			exit();
		} else {
			$registry->setErrors($errors);
		}
	} else {
		if (empty($errors)) {
		
		}
	}
	
	
	$view->render();
