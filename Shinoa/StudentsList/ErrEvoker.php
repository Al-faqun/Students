<?php
namespace Shinoa\StudentsList;


use Shinoa\StudentsList\Exceptions\StudentException;

class ErrEvoker
{
	function __construct()
	{
	}
	
	/**
	 * Checks
	 * @param array $array
	 * @return bool
	 */
	function isErrorIn(array $array)
	{
		if ( array_key_exists('evokeError', $array) ) {
			$result = true;
		} else $result = false;
		return $result;
	}
	
	function isExceptionIn(array $array)
	{
		if ( array_key_exists('evokeException', $array) ) {
			$result = true;
		} else $result = false;
		return $result;
	}
	
	function evokeError()
	{
		$test = new \stdClass();
		$test->callToNonExistantFunction();
	}
	
	function evokeException()
	{
		throw new StudentException('You triggered exception');
	}
	
	
}