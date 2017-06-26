<?php
namespace Shinoa\StudentList\Tests;

use Shinoa\StudentsList\FileSystem;
use PHPUnit\Framework\TestCase;

class FileSystemTest extends TestCase
{
	function testAppendFile()
	{
		$name = basename(__FILE__);
		$path = FileSystem::append(array(__DIR__, $name));
		$this->assertTrue(is_file($path));
	}
	
	function testAppendDirectory()
	{
		$name = basename(__DIR__);
		$path = FileSystem::append(array(dirname(__DIR__), $name));
		$this->assertTrue(is_dir($path));
	}
}
