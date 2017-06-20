<?php
namespace Shinoa\StudentsList\Views;
use Shinoa\StudentsList\Exceptions\ViewException;
/**
 * Abstract class CommonView
 * @package Shinoa\StudentsList
 */
abstract class CommonView
{
	protected $twig;
	
    /**
     * @var array Array of strings.
     */
    protected $requiredFields;
    
    
    /**
     * @var string Full path to the folder with templates
     */
	protected $templatesDir = '';
	
	/**
	 * CommonView constructor.
	 * @param string $templatesDir
	 */
	function __construct($templatesDir)
	{
		$this->templatesDir = $templatesDir;
	}
	
	/**
	 * Throws exception if at least one parameter is not defined.
	 * Exception text contains name of the missing key.
	 * @param $params
	 * @throws ViewException
	 */
	protected function ensureParams($params)
	{
		if ( $this->checkParams($params) !== true ) {
			$missing = $this->checkParams($params);
			throw new ViewException("Missing key: $missing");
		}
	}
	
    /**
     * Checks whether provided array has all the required fields to run the View.
     * Values of the $params array are not checked.
     * Returns bool any way.
     *
     * @param $params
     * @return bool|string|int TRUE if array has all the required keys; first missing key otherwise.
     */
	protected function checkParams($params)
    {
        $result = false;
        if (is_array($params)) {
            foreach ($this->requiredFields as $required) {
                if ( !array_key_exists($required, $params) ) {
                    $result = $required;
                    break;
                } else $result = true;
            }
        } else $result = false;
        return $result;
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
    function mesToHTML($messages)
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
     * @var $params array Link to the params array, from which are retrieved all the data.
     */
    function render($params)
	{
		//посылаем нужный заголовок
		header('Content-type: text/html; charset=utf-8');
		//отсылаем страницу пользователю
		$contents = $this->output($params);
		echo $contents;
	}
	

	
	
	
}