<?php
	use Shinoa\StudentsList\Controllers\ListController;
	use Shinoa\StudentsList\Loader;
	use Shinoa\StudentsList\StatusSelector;
	use Shinoa\StudentsList\ErrEvoker;
	require_once 'bootstrap.php';
	//timezone для логов
	date_default_timezone_set('Europe/Moscow');
	
	$controller = new ListController();
	$controller->post('appStatus', function ($key, $value, ListController $c) {
		$statusSelector = new StatusSelector();
		$code = $statusSelector->checkCode($value);
		if ( $code !== false) {
			$statusSelector->save($code);
		}
		$c->redirect('/Students/');
	});
	
	$controller->cookie('appStatus', function ($key, $value, ListController $c) {
		$statusSelector = new StatusSelector();
		$code = $statusSelector->checkCode($value);
			if ( $code !== false) {
				$c->setAppStatus($code);
			}
	});
	
	$controller->noCookie('appStatus', function ($key, $value, ListController $c) {
		$statusSelector = new StatusSelector();
		$code = $statusSelector->getDefaultCode(Loader::getConfig()->app->status);
		$c->setAppStatus($code);
	});
	
	$controller->get('evokeException', function ($key, $value, ListController $c) {
		ErrEvoker::evokeException();
	});
	
	$controller->get('evokeError', function ($key, $value, ListController $c) {
		ErrEvoker::evokeError();
	});

	$controller->start(dirname(__DIR__));
