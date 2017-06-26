<?php
/**
 * Created by PhpStorm.
 * User: Shinoa
 * Date: 26.06.2017
 * Time: 13:59
 */

namespace Shinoa\StudentList\Tests;

use Shinoa\StudentsList\Loader;
use PHPUnit\Framework\TestCase;

class LoaderTest extends TestCase
{
	function setUp()
	{
	}
	
	function tearDown()
	{
	}
	
	function testGetRoot()
	{
		$root = '/some/root';
		Loader::setRoot($root);
		$this->assertEquals($root, Loader::getRoot());
	}
	
	function testGetConfig()
	{
		$config = Loader::getConfig();
		$this->assertInstanceOf(\SimpleXMLElement::class, $config);
	}
	
}
