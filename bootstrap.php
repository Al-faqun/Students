<?php
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

	function shutDown()
	{
		$error = error_get_last();
		if (isset($error['type']) && $error['type'] === E_ERROR) {
			$errorHelper = new \Shinoa\StudentsList\ErrorHelper(dirname(__DIR__) . '\Students\templates');
			$errorHelper->renderFatalError($error, '');
		}
	}
	
	function errorHandler($errno, $errstr, $errfile, $errline)
	{
		$errorHelper = new \Shinoa\StudentsList\ErrorHelper(dirname(__DIR__) . '\Students\templates');
		
		$text = array();
		$text[] = "Возникла ошибка:";
		$text[] = 'текст: ';
		$text[] = \Shinoa\StudentsList\ErrorHelper::splitErrMes($errstr);
		$text[] = 'файл: ' . $errfile . ',';
		$text[] = 'строка:' . $errline . '.';
		
		$errorHelper->renderErrorPageAndExit($text, '');
		
		
	}
	
	spl_autoload_register('autoload');
	register_shutdown_function('shutDown');
	error_reporting(0);
	
	