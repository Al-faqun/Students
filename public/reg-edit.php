<?php
	use Shinoa\StudentsList\Controllers\RegEditController;
	use Shinoa\StudentsList\Loader;
	use Shinoa\StudentsList\StatusSelector;
	
	include_once '../bootstrap.php';

	$controller = new RegEditController();
	
	$controller->get('uploaded', function ($key, $value, RegEditController $c) {
		$c->addMessage('Ваши данные успешно добавлены!');
	});
	
	$controller->get('updated', function ($key, $value, RegEditController $c) {
		$c->addMessage('Ваши данные успешно обновлены!');
	});

	$controller->cookie('appStatus', function ($key, $value, RegEditController $c) {
		$statusSelector = new StatusSelector();
		$code = $statusSelector->checkCode($value);
		if ( $code !== false) {
			$c->setAppStatus($code);
		}
	});
	
	$controller->noCookie('appStatus', function ($key, $value, RegEditController $c) {
		$statusSelector = new StatusSelector();
		$code = $statusSelector->getDefaultCode(Loader::getConfig()->app->status);
		$c->setAppStatus($code);
	});
	
	$controller->start(dirname(__DIR__));