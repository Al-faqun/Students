<?php
	use Shinoa\StudentsList\Controllers\RegEditController;
use Shinoa\StudentsList\ErrorHelper;
use Shinoa\StudentsList\Loader;
	use Shinoa\StudentsList\StatusSelector;
	try {
		require_once '../bootstrap.php';
		date_default_timezone_set('Europe/Moscow');
		$root = dirname(__DIR__);
		Loader::setRoot($root);
		
		$errorHelper = new ErrorHelper(appendFilePath([$root, 'templates']));
		$errorHelper->setLogFilePath(appendFilePath([$root, 'errors.log']));
		$errorHelper->registerFallbacks(Loader::getStatus());
		
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
			if ($code !== false) {
				$c->setAppStatus($code);
			}
		});
		$controller->noCookie('appStatus', function ($key, $value, RegEditController $c) {
			$statusSelector = new StatusSelector();
			$code = $statusSelector->getDefaultCode(Loader::getConfig()->app->status);
			$c->setAppStatus($code);
		});
		
		$controller->start($root);
	} catch (\Throwable $e) {
		$errorHelper->dispatch($e);
	}