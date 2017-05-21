<?php

namespace Shinoa\StudentsList;

/**
 * Class Errorhelper can create error messages and correctly display them to user, or/and log them.
 *
 * @author Shinoa
 */
class ErrorHelper {
	/**
	 * @var string Holds path to folder with templates of pages
	 */
	private $templateDir = '';
	
	/**
	 * ErrorHelper constructor.
	 * @param $templateDir
	 * @throws \Exception
	 */
	public function __construct($templateDir) {
		if (is_dir($templateDir)) {
			$this->templateDir = $templateDir;
		} else throw new \Exception("Error Helper faied to be created");
	}
	
	/**
	 * Recursively walks provided exception and it's parents,
	 * creates text, holding info about all of them,
	 * renders error page with that info and exists script.
	 *
	 * @param \Throwable $e
	 * @param string $whereToRedirect Relative URLpath to desired location,
	 * to which user would be relocated after viewing info about exceptions.
	 */
	public function renderExceptionAndExit(\Throwable $e, $whereToRedirect)
	{
		//recursively get info about exception and it's possible parents
		$i = 1;
		$previous = $e->getPrevious();
		while ($previous !== null)
		{
			$i++;
			$previous = $previous->getPrevious();
		}
		
		$output = array();
		do {
			$text = array();
			$text[] = "Возникло исключение #{$i} класса " . get_class($e) . ':';
			$text[] = 'текст: ' . "'" . $e->getMessage() . "'" . ',';
			$text[] = 'файл: ' . $e->getFile() . ',';
			$text[] = 'строка:' . $e->getLine() . '.';
			$trace = explode('#', $e->getTraceAsString());
			foreach ($trace as $line) {
				if (!empty($line)) {
					$text[] = '#' . $line;
				}
			}
			$output[] = $text;
			$i--;
			$previous = $e->getPrevious();
		}
		while ($e = $previous);
		
		//show info to the user and end main script
		$this->renderErrorPageAndExit($output, $whereToRedirect);
	}
	
	/**
	 * Shows to user designed page about error with desired message and button "Get back"  to specified url.
	 * @param string|array $errorMes
	 * @param string $whereToRedirect Relative $whereToRedirect URLpath to desired location,
	 * to which user would be relocated after viewing info about exceptions.
	 */
	public function renderErrorPageAndExit($errorMes, $whereToRedirect) 
	{
		header('Content-type: text/html; charset=utf-8');
		$error = $errorMes;
		$form_action = $whereToRedirect;
		$form_method = 'post';
		$input_value = 'Вернуться';
		include $this->templateDir . '/error.html.php';	
		exit();
	}
	
	/**
	 * Shows to user page with detailedd desctiption of php error.
	 *
	 * @param array $error PHP error array(old error)
	 * @param string $whereToRedirect Relative $whereToRedirect URLpath to desired location,
	 * to which user would be relocated after viewing info about exceptions.
	 */
	public function renderFatalError($error, $whereToRedirect)
	{
		$text = array();
		$text[] = "Произошла фатальная ошибка, выполнение приложения прекращается:";
		$text[] = 'текст: ';
		$text[] = self::splitErrMes($error['message']);
		$text[] = 'файл: ' . $error['file'] . ',';
		$text[] = 'строка:' . $error['line'] . '.';
		
		$this->renderErrorPageAndExit($text, $whereToRedirect);
	}
	
	/**
	 * Render fatal error (Throwable) with text about error, not about exception.
	 *
	 * @param \Throwable $error
	 * @param string $whereToRedirect Relative $whereToRedirect URLpath to desired location,
	 * to which user would be relocated after viewing info about exceptions.
	 */
	public function renderFatalErrorObj(\Throwable $error, $whereToRedirect)
	{
		$text = self::errorToText($error);

		$this->renderErrorPageAndExit($text, $whereToRedirect);
	}
	
	/**
	 * @param array $message Array of strings, each string would be added to log as new line.
	 * @param string $logpath Full path to log file.
	 */
	public function addToLog($message, $logpath)
	{
		$text = self::arrayToString($message, PHP_EOL, PHP_EOL);
		error_log($text, 3, $logpath);
	}
	
	/**
	 * Splits error message into array of strings by delimiter (# as default).
	 * Useful to create pretty text for developer or to log.
	 *
	 * @param $message
	 * @return array
	 */
	public static function splitErrMes($message)
	{
		$lines = [];
		$array = explode('#', $message);
		foreach ($array as $line) {
			$lines[] = '#' . $line;
		}
		return $lines;
	}
	
	/**
	 * Converts array into string using new-line separators like '<br>' or PHP_EOL.
	 * Optionally you can use 'block separator', which is added after the string to indicate additional empty line.
	 *
	 * @param array $array Array of strings to convert.
	 * @param string $separator Indicates new line.
	 * @param string $blockSeparator Adds to the end of string.
	 * @return string
	 */
	public static function arrayToString($array, $separator, $blockSeparator = '')
	{
		$string = '';
		foreach ($array as $line) {
			if (is_array($line)) {
				$string .= self::arrayToString($line, $separator);
			} else {
				$string .= $line . $separator;
			}
		}
		if (!empty($blockSeparator)) {
			$string .= $blockSeparator;
		}
		return $string;
	}
	
	/**
	 *
	 * @param \Throwable $e
	 * @return array
	 */
	public static function errorToText(\Throwable $e)
	{
		$text = array();
		$text[] = "Возникла ошибка, выполнение приложения прекращено:";
		$text[] = 'текст: ';
		$text[] = self::splitErrMes($e->getMessage());
		$text[] = 'файл: ' . $e->getFile() . ',';
		$text[] = 'строка:' . $e->getLine() . '.';
		
		return $text;
	}
}

