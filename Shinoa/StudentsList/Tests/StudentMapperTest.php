<?php
namespace Shinoa\StudentsList\Tests;

use PHPUnit\Framework\TestCase;
use Shinoa\StudentsList\Exceptions\StudentException;
use Shinoa\StudentsList\StudentMapper;
use Shinoa\StudentsList\Student;
use Symfony\Component\Config\Definition\Exception\InvalidTypeException;
use Zend\Cache\Exception\UnexpectedValueException;

class StudentMapperTest extends TestCase
{
	private $pdo;
	private $SM;
	private $insertedIds = array();

	public function setUp()
	{
		$this->pdo = $GLOBALS['test_pdo'];
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
	
	public function testGetStudentsWrongNameFail()
	{
		$this->expectException(StudentException::class);
		
		$students = $this->SM->getStudents('nonexistant_name');
	}
	
	public function testGetStudentsWrongLimitFailExc()
	{
		$this->expectException(StudentException::class);

		$students = $this->SM->getStudents('surname', 'ASC', -5, 5);
	}
	
	public function testGetStudentsLimitAsString()
	{
		$students = $this->SM->getStudents('surname', 'ASC', '1', '5');
		$this->assertNotFalse($students);
	}

	public function testInsertStudent()
	{
		$student = new Student('mannanov', 'nikoly',  'М',
			                   'BGF5',     'manan@rambler.ru' , 259,
			                    1997,      'Приезжий');
		$result = $this->SM->insertStudent($student);
		array_push($this->insertedIds, $this->SM->lastInsertedId());
		$this->assertNotFalse($result);
	}
	
	public function testInsertStudentTooLongValueFailExc()
	{
		$this->expectException(StudentException::class);
		
		$student = new Student('mannanov', 'nikoly',  'М',
			'BGF5ee',     'manan@rambler.ru' , 259,
			1997,      'Приезжий');
		$result = $this->SM->insertStudent($student);
		array_push($this->insertedIds, $this->SM->lastInsertedId());

	}
	
	public function testInsertNotIntValueFailExc()
	{
		$this->expectException(StudentException::class);
		
		$student = new Student('mannanov', 'nikoly',  'М',
			'BGF5ee',     'manan@rambler.ru' , 'not_int_value',
			1997,      'Приезжий');
		$result = $this->SM->insertStudent($student);
		array_push($this->insertedIds, $this->SM->lastInsertedId());
		
	}
	
	public function testUpdateStudent()
	{
		$this->testInsertStudent();
		$student = new Student('updated', 'nikoly',  'М',
			'BGF53',     'manan@rambler.ru' , 259,
			1997,      'Приезжий');
		$id = end($this->insertedIds);
		if ($id !== false) {
			$result = $this->SM->updateStudent($student, $id);
		} else $result = false;
		$this->assertNotFalse($result);
	}
	
	
	public function testFindStudentByID()
	{
		
		$result = $this->SM->findStudentByID(1);

		$this->assertNotFalse($result);
	}
	
	public function testFindStudentByIDString()
	{
		$result = $this->SM->findStudentByID('1');
		$this->assertNotFalse($result);
	}
	
	public function testFindStudentByIDTooBigFail()
	{
		$result = $this->SM->findStudentByID(100000);
		
		$this->assertFalse($result);
	}
	
	public function testFindStudentByIDNegativeValueFailExc()
	{
		$this->expectException(\UnexpectedValueException::class);
		
		$result = $this->SM->findStudentByID(-10);
	}
}
