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
