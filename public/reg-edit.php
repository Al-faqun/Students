<?php
	use Shinoa\StudentsList\FileSystem;
	use Shinoa\StudentsList\Controllers\RegEditController;
	use Shinoa\StudentsList\ErrorHelper;
	use Shinoa\StudentsList\Loader;
	use Shinoa\StudentsList\StatusSelector;

	require_once '../bootstrap.php';
	$root = dirname(__DIR__);
	$errorHelper = new ErrorHelper(FileSystem::append([$root, 'templates']));
	try {
		
		date_default_timezone_set('Europe/Moscow');
		Loader::setRoot($root);
		$errorHelper->setLogFilePath(FileSystem::append([$root, 'public', 'errors.log']));
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