<?php
namespace Shinoa\StudentList\Tests;

use Shinoa\StudentsList\ErrEvoker;
use PHPUnit\Framework\TestCase;
use Shinoa\StudentsList\Exceptions\StudentException;

class ErrEvokerTest extends TestCase
{
	private $errorEvoker;
	public function setUp()
	{
		$this->errorEvoker = new ErrEvoker();
	}
	
	public function tearDown()
	{
	}
	
	function testIsErrorIn()
	{
		$input = array(ErrEvoker::$keyError => '');
		$result = $this->errorEvoker->isErrorIn($input);
		$this->assertTrue($result);
	}
	
	function testIsErrorInFail()
	{
		$input = array(ErrEvoker::$keyError . 'now not the key' => '');
		$result = $this->errorEvoker->isErrorIn($input);
		$this->assertFalse($result);
	}
	
	function testIsExceptionIn()
	{
		$input = array(ErrEvoker::$keyExcep => '');
		$result = $this->errorEvoker->isExceptionIn($input);
		$this->assertTrue($result);
	}
	
	function testIsExceptionInFail()
	{
		$input = array(ErrEvoker::$keyExcep . 'now not the key' => '');
		$result = $this->errorEvoker->isExceptionIn($input);
		$this->assertFalse($result);
	}
	
	function testEvokeError()
	{
		$this->expectException(\Error::class);
		$this->errorEvoker->evokeError();
	}
	
	function testEvokeException()
	{
		$this->expectException(StudentException::class);
		$this->errorEvoker->evokeException();
	}
}
