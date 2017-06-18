<?php
/**
 * Created by PhpStorm.
 * User: Shinoa
 * Date: 28.05.2017
 * Time: 23:36
 */

namespace Shinoa\StudentsList\Tests;

use Shinoa\StudentsList\DIContainer;
use PHPUnit\Framework\TestCase;
use Shinoa\StudentsList\Exceptions\DIContainerException;

class DIContainerTest extends TestCase
{
	private $container;
	public function setUp()
	{
		$this->container = new DIContainer();
	}
	
	public function tearDown()
	{
	
	}
	
	public function testRegisterGet()
	{
		$testValue = 'high five!';
		
		$this->container->register('call', function (DIContainer $container) use ($testValue) {
			return $testValue;
		} );
		$result = $this->container->get('call');
		
		$this->assertEquals($result, $testValue);
		
	}
	
	public function testRegisterGetTwoFactories()
	{
		$firstValue = 'high five!';
		$secondValue = 'die hard!';
		
		$this->container->register('first', function (DIContainer $container) use ($firstValue) {
			return $firstValue;
		} );
		$this->container->register('second', function (DIContainer $container) use ($secondValue) {
			return $container->get('first') . $secondValue;
		} );
		
		$result = $this->container->get('second');
		$this->assertEquals($result, $firstValue . $secondValue);
		
	}
	
	public function testGetKeyNotExistsFail()
	{
		$this->expectException(DIContainerException::class);
		$result = $this->container->get('this service is not registered');
		
	}
	
	public function testRegisterDublicateNamesFail()
	{
		$name = 'first';
		$this->container->register($name, function (DIContainer $container) {
			return 1;
		} );
		
		$this->expectException(DIContainerException::class);
		
		$this->container->register($name, function (DIContainer $container) {
			return 2;
		} );
		
		
	}
}

