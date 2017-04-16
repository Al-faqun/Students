<?php
namespace Shinoa\StudentsList\Tests;

use Shinoa\StudentsList\Student;
use Shinoa\StudentsList\StudentValidator;
use PHPUnit\Framework\TestCase;

class StudentValidatorTest extends TestCase
{
	private $studentValidator;
	private $errors;
	
	public function setUp()
	{
		$this->studentValidator = new StudentValidator();
		$this->errors = [];
		
	}
	
	public function tearDown()
	{
	}
	
	public function testCheck()
	{
		$this->studentValidator = new StudentValidator();
		$test = new Student('name', 'surname', 'masculine', 'grNm5',
			                'email@gmail.com', 400, 1994, 'Local');
		$student = $this->studentValidator->check($test, $errors);
		
		$this->assertNotFalse($student, $errors);
		
	}
	
	public function testCheckWrongNameFail()
	{
		$this->studentValidator = new StudentValidator();
		$test = new Student('', 'surname', 'masculine', 'grNum5',
			'email@gmail.com', 400, 1994, 'Local');
		$student = $this->studentValidator->check($test, $errors);
		
		$this->assertFalse($student);
		
	}
	
	public function testCheckWrongSurnameFail1()
	{
		$this->studentValidator = new StudentValidator();
		$test = new Student('name1', false, 'masculine', 'grNum5',
			'email@gmail.com', 400, 1994, 'Local');
		$student = $this->studentValidator->check($test, $errors);
		
		$this->assertFalse($student);
		
	}
	
	public function testCheckWrongSurnameFail2()
	{
		$this->studentValidator = new StudentValidator();
		$test = new Student('name1', '5', 'masculine', 'grNum5',
			'email@gmail.com', 400, 1994, 'Local');
		$student = $this->studentValidator->check($test, $errors);
		
		$this->assertFalse($student);
		
	}
	
	public function testCheckWrongSexFail()
	{
		$this->studentValidator = new StudentValidator();
		$test = new Student('name1', 'gfg', 'm', 'grNum5',
			'email@gmail.com', 400, 1994, 'Local');
		$student = $this->studentValidator->check($test, $errors);
		
		$this->assertFalse($student);
		
	}
	
	public function testCheckWrongEmailFail1()
	{
		$this->studentValidator = new StudentValidator();
		$test = new Student('name1', 'gfg', 'masculine', 'grNum5',
			'email@gmail', 400, 1994, 'Local');
		$student = $this->studentValidator->check($test, $errors);
		
		$this->assertFalse($student);
		
	}
	
	public function testCheckWrongEgeSumFail1()
	{
		$this->studentValidator = new StudentValidator();
		$test = new Student('name1', 'gfg', 'masculine', 'grNum5',
			'email@gmail', 'gf', 1994, 'Local');
		$student = $this->studentValidator->check($test, $errors);
		
		$this->assertFalse($student);
		
	}
	
	public function testCheckWrongEgeSumFail2()
	{
		$this->studentValidator = new StudentValidator();
		$test = new Student('name1', 'gfg', 'masculine', 'grNum5',
			'email@gmail', 700, 1994, 'Local');
		$student = $this->studentValidator->check($test, $errors);
		
		$this->assertFalse($student);
		
	}
	
	public function testCheckWrongYearBirthFail1()
	{
		$this->studentValidator = new StudentValidator();
		$test = new Student('name1', 'gfg', 'masculine', 'grNum5',
			'email@gmail', 300, 1234, 'Local');
		$student = $this->studentValidator->check($test, $errors);
		
		$this->assertFalse($student);
		
	}
	
	public function testCheckWrongYearBirthFail2()
	{
		$this->studentValidator = new StudentValidator();
		$test = new Student('name1', 'gfg', 'masculine', 'grNum5',
			'email@gmail', 300, 1976, 'Local');
		$student = $this->studentValidator->check($test, $errors);
		
		$this->assertFalse($student);
		
	}
	
	public function testCheckWrongLocationFail()
	{
		$this->studentValidator = new StudentValidator();
		$test = new Student('name1', 'gfg', 'masculine', 'grNum5',
			'email@gmail', 300, 'abra', 'fgfg');
		$student = $this->studentValidator->check($test, $errors);
		
		$this->assertFalse($student);
		
	}
}
