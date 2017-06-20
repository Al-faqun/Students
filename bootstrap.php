<?php
	use \Shinoa\StudentsList\ErrorHelper;
	
	//не забывать поддерживать значения констант на том же уровне, что и в Registry
	define('APP_IN_DEVELOPMENT', 0);
	define('APP_IN_PRODUCTION', 1);
	//переменные, которые нельзя unset
	//устанавливаем статус для того, чтобы обработчики работали независимо от классов
	$appStatus = APP_IN_DEVELOPMENT;

	/**
	 * Creates OS-independent path from array of folders or files
	 * @param array $folders array of strings, WITHOUT delimiters '/', '\',
	 * except first folder in linux system must be prepended with '/'
	 * @return string valid path
	 */
	function appendFilePath(array $folders)
	{
		$firstFolder = rtrim($folders[0], "\t\n\r\0\x0B\\\/");
		$path = $firstFolder . DIRECTORY_SEPARATOR;
		for ( $i = 1; $i < count($folders); $i++ ) {
			$nextFolder = trim($folders[$i], "\t\n\r\0\x0B\\\/");
			$path.= $nextFolder . DIRECTORY_SEPARATOR;
		}
		$path = rtrim($path, "\t\n\r\0\x0B\\\/");
		return $path;
	}

	//бывает, что и в php7 выбрасываются несловимые ошибки
	function shutDown()
	{
		$error = error_get_last();
		if (isset($error['type']) && $error['type'] === E_ERROR) {
			$errorHelper = new ErrorHelper( appendFilePath([__DIR__,'templates']) );
			$errorHelper->renderFatalError($error, '');
		}
	}
	
	
	//обработчики ошибок "if everything else fails"
	//не расчитывайте на них, программируйте обработку ошибок в скриптах
	function errorHandler($errno, $errstr, $errfile, $errline)
	{
		$root = __DIR__;
		$errorHelper = new ErrorHelper( appendFilePath([$root, 'templates']) );
		$text = array();
		$text[] = "Возникла ошибка, выполнение приложения могло бы быть продолжено:";
		$text[] = 'текст: ';
		$text[] = \Shinoa\StudentsList\ErrorHelper::splitErrMes($errstr);
		$text[] = 'файл: ' . $errfile . ',';
		$text[] = 'строка:' . $errline . '.';
		switch ($GLOBALS['appStatus']) {
			case APP_IN_DEVELOPMENT:
				$errorHelper->renderErrorPageAndExit($text, '/');
				break;
				
			case APP_IN_PRODUCTION:
				$userMes = 'Encountered error, logs are sent to developer. Please, try again later!';
				array_unshift($text, date('d-M-Y H:i:s') . ' ');
				$logpath  = appendFilePath( [$root, 'public', 'errors.log'] );
				$errorHelper->addToLog($text, $logpath);
				$errorHelper->renderErrorPageAndExit($userMes, '/');
				break;
		}

		return true;
		
	}
	
	function exceptionHandler(Throwable $e)
	{
		$root = __DIR__;
		$errorHelper = new ErrorHelper( appendFilePath([$root, 'templates']) );
		switch ($GLOBALS['appStatus']) {
			case APP_IN_DEVELOPMENT:
				$errorHelper->renderExceptionAndExit($e, '/');
				break;
			case APP_IN_PRODUCTION:
				$userMes = 'Encountered error, logs are sent to developer. Please, try again later!';
				//форматируем текст для записи в лог-файл
				$text = ErrorHelper::errorToArray($e);
				array_unshift($text, date('d-M-Y H:i:s') . ' ');
				$logpath = appendFilePath( [$root, 'public', 'errors.log'] );
				$errorHelper->addToLog($text, $logpath);
				$errorHelper->renderExceptionAndExit($e, '/');
				break;
		}
	}

    //автозагрузчик Composer'а
    include_once __DIR__ . '/vendor/autoload.php';
	//на случай, если какая-то фатальная ошибка пробралась и прекратила скрипт
	register_shutdown_function('shutDown');
	//для нефатальных ошибок, warning и notices выбрасывают Throwable
	set_error_handler('errorHandler', E_ALL);
	//для throwable
	set_exception_handler('exceptionHandler');
	//user must see no thing
	//error_reporting(0);



	