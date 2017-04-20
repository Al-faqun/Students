<?php
namespace Shinoa\StudentsList\Tests;

use Shinoa\StudentsList\Exceptions\DbException;
use Shinoa\StudentsList\PasswordMapper;
use PHPUnit\Framework\TestCase;

class PasswordMapperTest extends TestCase
{
	private $pdo;
	private $mapper;
	private $insertedIds = array();
	
	public function setUp()
	{
		$opt = array(
			\PDO::ATTR_ERRMODE            => \PDO::ERRMODE_EXCEPTION,
			\PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
			\PDO::ATTR_EMULATE_PREPARES   => false);
		$dsn = 'mysql:host=localhost;dbname=students_book;charset=utf8';
		$this->pdo = new \PDO($dsn, 'root', 'VtVgfhfif354', $opt);
		$this->mapper = new PasswordMapper($this->pdo);
	}
	
	public function tearDown()
	{
		if ( !empty($this->insertedIds)) {
			foreach ($this->insertedIds as $userId) {
				$this->mapper->deleteHash($userId);
			}
		}
	}
	
	public function testGetHash()
	{
		$userid = 1;
		$hash = $this->mapper->getHash($userid);
	
		$this->assertNotFalse($hash);
	}
	
	public function testGetHashIDAsString()
	{
		$userid = '1';
		$hash = $this->mapper->getHash($userid);
		
		$this->assertNotFalse($hash);
	}
	
	public function testGetHashWrongIDFail()
	{
		$userid = 700000;
		$hash = $this->mapper->getHash($userid);
		
		$this->assertFalse($hash);
	}
	
	public function testGetHashIDAsStringFail()
	{
		$userid = 'fdfdf1';
		$hash = $this->mapper->getHash($userid);
		
		$this->assertFalse($hash);
	}
	
	public function testGetHashIDAsBool()
	{
		$userid = true;
		$hash = $this->mapper->getHash($userid);
		
		$this->assertNotFalse($hash);
	}
	
	public function testGetHashIDAsArrayFail()
	{
		$userid = array('dssd');
		$hash = $this->mapper->getHash($userid);
		
		$this->assertFalse($hash);
	}
	
	public function testAddHash()
	{
		$userid = 10000;
		$hash = 'this hash must have been deleted, if you see it - blame test script';
		$result = $this->mapper->addHash($userid, $hash);
		
		array_push($this->insertedIds, $userid);
		$this->assertNotFalse($result);
	}
	
	public function testAddHashString()
	{
		$userid = '10000';
		$hash = 'this hash must have been deleted, if you see it - blame test script';
		$result = $this->mapper->addHash($userid, $hash);
		
		array_push($this->insertedIds, $userid);
		$this->assertNotFalse($result);
	}
	
	public function testAddHashArrayFail()
	{
		$userid = array('jyj');
		$hash = 'this hash must have been deleted, if you see it - blame test script';
		$result = $this->mapper->addHash($userid, $hash);
		
		$this->assertFalse($result);
	}
	
	public function testUpdateHash()
	{
		$this->testAddHash();
		$userid = 10000;
		$hash = 'updated hash, if you see it - blame test script';
		$result = $this->mapper->updateHash($userid, $hash);
		
		$this->assertNotFalse($result);
	}
	
	public function testUpdateHashString()
	{
		$this->testAddHash();
		$userid = '10000';
		$hash = 'updated hash, if you see it - blame test script';
		$result = $this->mapper->updateHash($userid, $hash);
		
		$this->assertNotFalse($result);
	}
	
	public function testUpdateHashArrayFail()
	{
		$this->testAddHash();
		$userid = array('jyj');
		$hash = 'updated hash, if you see it - blame test script';
		$result = $this->mapper->updateHash($userid, $hash);
		
		$this->assertFalse($result);
	}
	
}
