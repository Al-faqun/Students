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