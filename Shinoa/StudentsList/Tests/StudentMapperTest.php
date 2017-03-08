<?php
namespace Shinoa\StudentsList\Tests;

use PHPUnit\Framework\TestCase;
use Shinoa\StudentsList\Exceptions\StudentException;
use Shinoa\StudentsList\StudentMapper;
use Shinoa\StudentsList\Student;

class StudentMapperTest extends TestCase
{
	private $pdo;
	private $SM;
	private $insertedIds = array();

	public function setUp()
	{
		$opt = array(
			\PDO::ATTR_ERRMODE            => \PDO::ERRMODE_EXCEPTION,
			\PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
			\PDO::ATTR_EMULATE_PREPARES   => false);
		$dsn = 'mysql:host=localhost;dbname=students_book;charset=utf8';
		$this->pdo = new \PDO($dsn, 'root', 'VtVgfhfif354', $opt);
		$this->SM = new StudentMapper($this->pdo);
	}

	public function tearDown()
	{
		if ( !empty($this->insertedIds)) {
			foreach ($this->insertedIds as $id) {
				$this->SM->deleteStudentByID($id);
			}
		}
	}

	public function testGetStudents()
	{
		$students = $this->SM->getStudents();
		$this->assertNotFalse($students);
	}

	public function testGetStudentsWrongParameters()
	{
		$this->expectException(StudentException::class);

		$students = $this->SM->getStudents('surname', 'ASC', -5, 5);
	}

	public function testInsertStudent()
	{
		$student = new Student('mannanov', 'nikoly',  'М',
			                   'BGF5',     'manan@rambler.ru' , 259,
			                    1997,      'Приезжий');
		$result = $this->SM->insertStudent($student);
		array_push($this->insertedIds, (int)$this->pdo->lastInsertId());
		$this->assertNotFalse($result);
	}

	public function testFindStudentByID()
	{
		$this->SM = new StudentMapper($this->pdo);
		$result = $this->SM->findStudentByID(1);

		$this->assertNotFalse($result);
	}

	public function testFindStudentByIDStringFail()
	{
		$this->SM = new StudentMapper($this->pdo);
		$result = $this->SM->findStudentByID('2');

		$this->assertFalse($result);
	}
}
