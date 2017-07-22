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
		
		
		$controller->start($root);
	} catch (\Throwable $e) {
		$errorHelper->dispatch($e);
	}