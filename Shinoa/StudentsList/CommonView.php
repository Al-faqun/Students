<?php
namespace Shinoa\StudentsList;

/**
 * Abstract class CommonView
 * @package Shinoa\StudentsList
 */
abstract class CommonView
{
	//link to the registry object, from which are retrieved all the data.
	protected $registry;
	//interactions with the database
	protected $dataMapper;
	//full path to the folder with templates
	protected $templatesDir = '';
	
	/**
	 * CommonView constructor.
	 * @param Registry $registry
	 * @param string $templatesDir
	 */
	function __construct(Registry $registry, $templatesDir)
	{
		$this->registry = $registry;
		$this->templatesDir = $templatesDir;
	}
	
	/**
	 * Escapes value for html use.
	 *
	 * @param $value
	 * @return string Value ready for html output.
	 */
	public static function esc($value)
	{
		$result = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
		return $result;
	}
	
	/**
	 * Escapes value of every member of array.
	 *
	 * @param array $array (by ref) A. of strings, every string to be escaped.
	 */
	public static function escAll(&$array)
	{
		foreach ($array as $key => $value) {
			$value = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
			$array[$key] = $value;
		}
	}
	
	/**
	 * Converts array to use in html (escapes, puts each line into <p> etc).
	 *
	 * @param array $messages
	 * @return string
	 */
	public function mesToHTML($messages)
	{
		$result = '';
		if (!empty($messages)) {
			foreach ($messages as $message) {
				$message = self::esc($message);
				$result .= "<p>$message</p>";
			}
		}
		return $result;
	}
	
	/**
	 * Outputs page to user.
	 */
	public function render()
	{
		//посылаем нужный заголовок
		header('Content-type: text/html; charset=utf-8');
		//отсылаем страницу пользователю
		$contents = $this->output();
		echo $contents;
	}
	
	/**
	 * Must return code of page.
	 *
	 * @return string
	 */
	abstract function output();
	
	
	
	
}