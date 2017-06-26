<?php
	use Shinoa\StudentsList\FileSystem;
	use Shinoa\StudentsList\Controllers\ListController;
	use Shinoa\StudentsList\ErrorHelper;
	use Shinoa\StudentsList\Loader;
	use Shinoa\StudentsList\StatusSelector;
	use Shinoa\StudentsList\ErrEvoker;
	
	require_once '../bootstrap.php';
	$root = dirname(__DIR__);
	$errorHelper = new ErrorHelper(FileSystem::append([$root, 'templates']));
	try {
		//timezone для логов
		date_default_timezone_set('Europe/Moscow');
		Loader::setRoot($root);
		$errorHelper->setLogFilePath(FileSystem::append([$root, 'public', 'errors.log']));
		$errorHelper->registerFallbacks(Loader::getStatus());
		
		$controller = new ListController();
		$controller->post('appStatus', function ($key, $value, ListController $c) {
			$statusSelector = new StatusSelector();
			$code = $statusSelector->checkCode($value);
			if ($code !== false) {
				$statusSelector->save($code);
			}
			$c->redirect('/');
		});
		$controller->cookie('appStatus', function ($key, $value, ListController $c) {
			$statusSelector = new StatusSelector();
			$code = $statusSelector->checkCode($value);
			if ($code !== false) {
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
		
		$controller->start($root);
	} catch (\Throwable $e) {
		$errorHelper->dispatch($e, Loader::getStatus());
	}
