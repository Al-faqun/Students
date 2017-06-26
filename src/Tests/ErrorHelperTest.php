<?php
namespace Shinoa\StudentList\Tests;

use Shinoa\StudentsList\ErrorHelper;
use PHPUnit\Framework\TestCase;

class ErrorHelperTest extends TestCase
{
	private $helper;
	
	function setUp()
	{
		$templates = dirname(__FILE__, 3) . DIRECTORY_SEPARATOR . 'templates';
		$this->helper = new ErrorHelper($templates);
	}
	
	function tearDown()
	{
	}
	
	function testArrayToString()
	{
		$array = ['first line', 'second line', 'third line'];
		$result = ErrorHelper::arrayToString($array, '<br>');
		$this->assertInternalType('string', $result);
		$this->assertContains('<br>', $result);
	}
	
	function testSplitErrMes()
	{
		$message = '#first line' . '#second line' . '#third line';
		$result = ErrorHelper::splitErrMes($message);
		$this->assertInternalType('array', $result);
	}
	
	function testExcepTextRecursiveException()
	{
		$e = new \Exception('sometext');
		$result = ErrorHelper::excepTextRecursive($e);
		$this->assertInternalType('array', $result);
	}
	
	function testExcepTextRecursiveError()
	{
		$e = new \Error('sometext');
		$result = ErrorHelper::excepTextRecursive($e);
		$this->assertInternalType('array', $result);
	}
	
	function testErrorToArray()
	{
		$error['message'] = 'Some message';
		$error['file'] = 'some file';
		$error['line'] = 50;
		$result = ErrorHelper::errorToArray($error);
		self::assertInternalType('array', $result);
	}
	
	
}
