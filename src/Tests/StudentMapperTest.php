<?php
namespace Shinoa\StudentsList\Tests;

use PHPUnit\Framework\TestCase;
use Shinoa\StudentsList\Database\StudentMapper;
use Shinoa\StudentsList\Exceptions\StudentException;
use Shinoa\StudentsList\Input\SearchQueryValidator;
use Shinoa\StudentsList\SearchData;
use Shinoa\StudentsList\Student;
use UnexpectedValueException;
use InvalidArgumentException;

class StudentMapperTest extends TestCase
{
	private $pdo;
	private $SM;
	private $insertedIds = array();

	public function setUp()
	{
		$this->pdo = $GLOBALS['test_pdo'];
		$this->pdo->beginTransaction();
		$this->SM = new StudentMapper($this->pdo);
		
	}

	public function tearDown()
	{
		$this->pdo->rollBack();
	}

	public function testGetStudents()
	{
		$validator = new SearchQueryValidator(array());
		$searchData = $validator->genSearchData();
		$students = $this->SM->getStudents($searchData);
		$this->assertNotFalse($students);
	}
	
	public function testGetStudentsWrongFieldFail()
	{
		$validator = new SearchQueryValidator(array());
		$searchData = $validator->genSearchData();
		$searchData->setSearchText('sometext');
		$searchData->setSearchField('nonexistant_name');
		$this->expectException(StudentException::class);
		$students = $this->SM->getStudents($searchData);
	}
	
	public function testGetStudentsWrongOffsetFailExc()
	{
		$validator = new SearchQueryValidator(array());
		$searchData = $validator->genSearchData();
		$searchData->setOffset(-5);
		$searchData->setLimit(5);
		$this->expectException(StudentException::class);
		$students = $this->SM->getStudents($searchData);
	}
	
	public function testGetStudentsLimitAsString()
	{
		$validator = new SearchQueryValidator(array());
		$searchData = $validator->genSearchData();
		$searchData->setOffset('1');
		$searchData->setLimit('5');
		$students = $this->SM->getStudents($searchData);
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
