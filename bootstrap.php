<?php
	//не забывать поддерживать значения констант на том же уровне, что и в спец. классах
	define('APP_IN_DEVELOPMENT', 0);
	define('APP_IN_PRODUCTION', 1);
	//устанавливаем статус для того, чтобы обработчики работали независимо от классов
	$status = APP_IN_DEVELOPMENT;
	
	function autoload($className)
	{
		$root = dirname(__DIR__);
		$className = ltrim($className, '\\');
		$fileName = '';
		$namespace = '';
		if ($lastNsPos = strrpos($className, '\\')) {
			$namespace = substr($className, 0, $lastNsPos);
			$className = substr($className, $lastNsPos + 1);
			$fileName = str_replace('\\', DIRECTORY_SEPARATOR, $namespace) . DIRECTORY_SEPARATOR;
		}
		$fileName .= str_replace('_', DIRECTORY_SEPARATOR, $className) . '.php';
		
		$path = $root . DIRECTORY_SEPARATOR . 'Students' . DIRECTORY_SEPARATOR . $fileName;
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
		if ($status = APP_IN_DEVELOPMENT) {
			$errorHelper = new \Shinoa\StudentsList\ErrorHelper(dirname(__DIR__) . '\Students\templates');
			
			$text = array();
			$text[] = "Возникла ошибка, выполнение приложения могло бы быть продолжено:";
			$text[] = 'текст: ';
			$text[] = \Shinoa\StudentsList\ErrorHelper::splitErrMes($errstr);
			$text[] = 'файл: ' . $errfile . ',';
			$text[] = 'строка:' . $errline . '.';
			
			$errorHelper->renderErrorPageAndExit($text, '');
		}
		else
		/* Не запускаем внутренний обработчик ошибок PHP */
		return true;
		
	}
	
	function exceptionHandler(Throwable $e)
	{
		$errorHelper = new \Shinoa\StudentsList\ErrorHelper(dirname(__DIR__) . '\Students\templates');
		$errorHelper->renderExceptionAndExit($e, '');
	}
	
	spl_autoload_register('autoload');
	//register_shutdown_function('shutDown'); - не нужно, т.к. php7 выбрасывает fatal error  в виде исключений
	//для нефатальных ошибок
	set_error_handler('errorHandler');
	set_exception_handler('exceptionHandler');
	error_reporting(0);
	
	unset($status);
	
	
	