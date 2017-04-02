<?php

namespace Shinoa\StudentsList;

/**
 * Description of ErrorHelper
 *
 * @author Shinoa
 */
class ErrorHelper {
	private $templateDir = '';
	
	public function __construct($templateDir) {
		if (is_dir($templateDir)) {
			$this->templateDir = $templateDir;
		} else throw new Exception("Error Helper faied to be created");
	}
	
	public function renderExceptionAndExit(\Throwable $e, $whereToRedirect)
	{
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
		
		$this->renderErrorPageAndExit($output, $whereToRedirect);
	}
	
	
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
	
	public static function splitErrMes($message)
	{
		$array = explode('#', $message);
		foreach ($array as $line) {
			$lines[] = '#' . $line;
		}
		return $lines;
	}
}

/*	try {
		throw new \Exception('Первое исключение');
	} catch (\Exception $e) {
		try {
			throw new \Exception('Второе исключение', 0, $e);
		} catch (\Exception $e) {
			try {
				throw new \Exception('Третье исключение', 0, $e);
			} catch (\Exception $e) {
				$errorhelper = new ErrorHelper('D:\USR\apache\htdocs\s1.localhost\Students\templates');
				$errorhelper->renderExceptionAndExit($e);
			}
		}
	}
*/
