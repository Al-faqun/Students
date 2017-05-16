<?php
namespace Shinoa\StudentsList;


use Shinoa\StudentsList\Exceptions\StudentException;

class StatusSelector
{
	private static $key = 'appStatus';
	private static $const = array('APP_IN_DEVELOPMENT', 'APP_IN_PRODUCTION');
	private static $texts = array('In development', 'In production');
	
	function __construct()
	{
	}
	
	
	function dataIn(array $input)
	{
		if (array_key_exists('appStatus', $input)) {
			switch ($input['appStatus']) {
				case '0':
					$result = self::$texts[0];
					break;
				case '1':
					$result = self::$texts[1];
					break;
				default:
					$result = false;
					break;
			}
		} else $result = false;
		return $result;
		
	}
	
	function save($value)
	{
		if ( !array_key_exists(self::$key, $_COOKIE) || ($_COOKIE[self::$key] !== $value) ) {
			setcookie(self::$key, $value, time() + 60 * 60 * 24 * 360,
				 null, null, null, true);
		}
	}
	
	public static function textToCode($statusText)
	{
		switch ($statusText) {
			case self::$texts[0]:
				$result = 0;
				break;
			case self::$texts[1]:
				$result = 1;
				break;
			default:
				$result = false;
				break;
		}
		return $result;
	}
	
	public static function codeToText($statusText)
	{
		switch ($statusText) {
			case 0:
				$result = self::$texts[0];
				break;
			case 1:
				$result = self::$texts[1];
				break;
			default:
				$result = false;
				break;
		}
		return $result;
	}
}