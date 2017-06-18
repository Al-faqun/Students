<?php
namespace Shinoa\StudentsList;


use Shinoa\StudentsList\Exceptions\StudentException;

/**
 * Class ErrEvoker encorporates means to check,
 * whether particular test error or exception must be triggered,
 * and also methods to trigger them.
 *
 * @package Shinoa\StudentsList
 */
class ErrEvoker
{
	public static $keyError = 'evokeError';
	public static $keyExcep = 'evokeException';
	function __construct()
	{
	}
	
	/**
	 * Checks whether command to evoke error exists in provided user input.
	 * @param array $array User input.
	 * @return bool True if command to evoke error exists, else false.
	 */
	function isErrorIn(array $array)
	{
		if ( array_key_exists(self::$keyError, $array) ) {
			$result = true;
		} else $result = false;
		return $result;
	}
	
	/**
	 * Checks whether command to evoke exception exists in provided user input.
	 * @param array $array User input.
	 * @return bool True if command to evoke exception exists, else false.
	 */
	function isExceptionIn(array $array)
	{
		if ( array_key_exists(self::$keyExcep, $array) ) {
			$result = true;
		} else $result = false;
		return $result;
	}
	
	/**
	 * Evokes error (by calling nonexistant method of existant class).
	 * The error can be catched as Throwable.
	 */
	static function evokeError()
	{
		$test = new \stdClass();
		$test->callToNonExistantFunction();
	}
	
	/**
	 * Evokes default exeption of the project.
	 * @throws StudentException
	 */
	static function evokeException()
	{
		throw new StudentException('You triggered exception');
	}
	
	
}