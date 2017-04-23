<?php
	//не забывать поддерживать значения констант на том же уровне, что и в спец. классах
	define('APP_IN_DEVELOPMENT', 0);
	define('APP_IN_PRODUCTION', 1);
	//переменные, которые нельзя unset
	//устанавливаем статус для того, чтобы обработчики работали независимо от классов
	$appStatus = APP_IN_DEVELOPMENT;
	
	function autoload($className)
	{
		//for psr-4: $root эквивалентно $base_dir
		$base_dir = __DIR__ . DIRECTORY_SEPARATOR;
		$className = ltrim($className, '\\');
		$fileName = '';
		$namespace = '';
		if ($lastNsPos = strrpos($className, '\\')) {
			$namespace = substr($className, 0, $lastNsPos);
			$className = substr($className, $lastNsPos + 1);
			$fileName = str_replace('\\', DIRECTORY_SEPARATOR, $namespace) . DIRECTORY_SEPARATOR;
		}
		$fileName .= str_replace('_', DIRECTORY_SEPARATOR, $className) . '.php';
		
		$path = $base_dir  . $fileName;
		if (file_exists($path)) {
			require $path;
		}
	}

	/* function shutDown()
	{
		$error = error_get_last();
		if (isset($error['type']) && $error['type'] === E_ERROR) {
			$errorHelper = new \Shinoa\StudentsList\ErrorHelper(dirname(__DIR__) . '\Students\templates');
			$errorHelper->renderFatalError($error, '');
		}
	}
	*/
	
	function errorHandler($errno, $errstr, $errfile, $errline)
	{
		$errorHelper = new \Shinoa\StudentsList\ErrorHelper(dirname(__DIR__) . '\Students\templates');
		$text = array();
		$text[] = "Возникла ошибка, выполнение приложения могло бы быть продолжено:";
		$text[] = 'текст: ';
		$text[] = \Shinoa\StudentsList\ErrorHelper::splitErrMes($errstr);
		$text[] = 'файл: ' . $errfile . ',';
		$text[] = 'строка:' . $errline . '.';
		switch ($GLOBALS['appStatus']) {
			case APP_IN_DEVELOPMENT:
				$errorHelper->renderErrorPageAndExit($text, '');
				break;
				
			case APP_IN_PRODUCTION:
				$userMes = 'Encountered error, logs are sent to developer. Please, try again later!';
				array_unshift($text, date('d-M-Y H:i:s') . ' ');
				$logpath = __DIR__ . DIRECTORY_SEPARATOR . 'errors.log';
				$errorHelper->addToLog($text, dirname(__DIR__) . '/errors.log');
				$errorHelper->renderErrorPageAndExit($userMes, '');
				break;
		}

		return true;
		
	}
	
	function exceptionHandler(Throwable $e)
	{
		switch ($GLOBALS['appStatus']) {
			case APP_IN_DEVELOPMENT:
				$errorHelper = new \Shinoa\StudentsList\ErrorHelper(dirname(__DIR__) . '\Students\templates');
				$errorHelper->renderExceptionAndExit($e, '');
				break;
			case APP_IN_PRODUCTION:
				$errorHelper = new \Shinoa\StudentsList\ErrorHelper(dirname(__DIR__) . '\Students\templates');
				$errorHelper->renderExceptionAndExit($e, '');
				break;
		}
	}
	
	spl_autoload_register('autoload');
	//register_shutdown_function('shutDown'); - не нужно, т.к. php7 выбрасывает fatal error  в виде исключений
	//для нефатальных ошибок
	set_error_handler('errorHandler', E_ALL);
	set_exception_handler('exceptionHandler');
	error_reporting(0);

	
	
	