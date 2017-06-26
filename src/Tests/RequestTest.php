<?php
namespace Shinoa\StudentList\Tests;

use Shinoa\StudentsList\Controllers\Request;
use PHPUnit\Framework\TestCase;

class RequestTest extends TestCase
{
	public function testConstructAndCall()
	{
		$assertion = 'success';
		$key = 'testkey';
		$value = 'testvalue';
		$input = array($key => $value);
		$callable = function ($key, $value, $controller) {
			return 'success';
		};
		$request = new Request($input, $key, $callable, null);
		$this->assertEquals($assertion, $request->call());
	}
	
	public function testConstructAndCallInvert()
	{
		$assertion = 'success';
		$nonexistantKey = 'nullkey';
		$existingKey = 'somekey';
		$value = 'testvalue';
		$input = array($existingKey => $value);
		$callable = function ($key, $value, $controller) {
			return 'success';
		};
		$request = new Request($input, $nonexistantKey, $callable, null, true);
		$this->assertEquals($assertion, $request->call());
		
	}
	
	public function testConstructAndCallFail()
	{
		$assertion = false;
		$nonexistantKey = 'nullkey';
		$existingKey = 'somekey';
		$value = 'testvalue';
		$input = array($existingKey => $value);
		$callable = function ($key, $value, $controller) {
			return 'success';
		};
		$request = new Request($input, $nonexistantKey, $callable, null, false);
		$this->assertEquals($assertion, $request->call());
	}
	
	public function testConstructAndCallInvertFail()
	{
		$assertion = false;
		$key = 'testkey';
		$value = 'testvalue';
		$input = array($key => $value);
		$callable = function ($key, $value, $controller) {
			return 'success';
		};
		$request = new Request($input, $key, $callable, null, true);
		$this->assertEquals($assertion, $request->call());
	}
}
