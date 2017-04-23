<?php
	include_once 'bootstrap.php';
	include_once __DIR__ . '/vendor/autoload.php';
	use \Shinoa\StudentsList\Registry;
	use \Shinoa\StudentsList\StudentMapper;
	use \Shinoa\StudentsList\RegEditView;
	use \Shinoa\StudentsList\LoginManager;
	use \Shinoa\StudentsList\PasswordMapper;
	
	//инициализация основных классов
	$root = dirname(__DIR__);
	$registry = Registry::getInstance();
	$registry->init($root, $root . '/Students/ini/config_test.xml');
	$dataMapper = new StudentMapper( $registry->getPDO() );
	$loginMan   = new LoginManager( new PasswordMapper( $registry->getPDO() ), $_COOKIE );
	$view       = new RegEditView( $registry, $registry->getRoot() . '/Students/templates' );
	//добавляем их в регистр, для единообразия доступа к ним другив классов
	$registry->setDataMapper($dataMapper);
	$registry->setLoginManager($loginMan);
	$registry->setView($view);

	//ошибки заполнения данных студента при регистрации или обновлении
	$errors = array();
	$validator = new \Shinoa\StudentsList\StudentValidator($_POST);
	//если данные формы заполнены верно, возвращает class Student, во всех остальных случаях - false
	$student = $validator->checkInput($_POST, $errors, $dataSent);
	$loginMan->checkAuth();
	//если юзер не залогинен - значит, он ещё не добавлял свои данные в БД
	if ( $loginMan->isLogged() === false ) {
		//если данные вообще были посланы -
		//только тогда выполняем какие-либо манипуляции
		if ($dataSent) {
			//данные посланы, ошибок нет - можно добавлять в БД
			if (empty($errors)) {
				$dataMapper->insertStudent($student);
				$insertedID = $dataMapper->lastInsertedId();
				$loginMan->logIn($insertedID);
				//редирект для очищения POST
				header('Location: reg-edit.php?uploaded', true, 303);
				exit();
			} else {
				//заполненный ошибками массив сохраняем для View
				$registry->setErrors($errors);
			}
		}
	//если юзер залогинен, значит, его данные уже добавлены в БД
	} elseif ( $loginMan->isLogged() === true ) {
		if ($dataSent) {
			//данные посланы, ошибок нет - можно  передавать обновлённыеданные в БД
			if (empty($errors)) {
				$dataMapper->updateStudent($student, $loginMan->getLoggedID());
				//редирект для очищения POST
				header('Location: reg-edit.php?updated', true, 303);
				exit();
			} else {
				$registry->setErrors($errors);
			}
		}
	}

	//сообщения в случае успешно выполненных действий, после редиректа
	//не знаю, как красиво оформить этот код
	if ( isset($_GET['uploaded']) ) {
		//сохраняем сообщение для View
		$registry->addMessage('Ваши данные успешно добавлены!');
	}
	if ( isset($_GET['updated']) ) {
		$registry->addMessage('Ваши данные успешно обновлены!');
	}
	
	//отображает страницу на основе собранных выше данных
	$view->render();
