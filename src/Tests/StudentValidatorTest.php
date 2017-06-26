<?php
namespace Shinoa\StudentsList\Tests;

use Shinoa\StudentsList\Database\StudentMapper;
use Shinoa\StudentsList\Input\StudentValidator;
use Shinoa\StudentsList\Student;
use PHPUnit\Framework\TestCase;

class StudentValidatorTest extends TestCase
{
	private $pdo;
	private $studentValidator;
	private $errors;
	
	public function setUp()
	{
		$this->pdo = $GLOBALS['test_pdo'];
		$this->errors = [];
		
	}
	
	public function tearDown()
	{
	}
	
	public function testCheckInput()
	{
		$input = array('form_sent' => '1', 'name' => 'testname', 'surname' => 'testsurname', 'sex' => 'masculine',
			                               'group_num' => 'grNm5', 'email' => 'email@gmail.com', 'ege_sum' => 400,
			                               'birth_year' => 1994, 'location' => 'Local');
		$this->studentValidator = new StudentValidator(new StudentMapper($this->pdo), $input);
		$datasent = $this->studentValidator->dataSent();
		$student = $this->studentValidator->checkInput($errors, $datasent);
		
		$this->assertNotFalse($student, $errors);
		
	}
	
	public function testCheckInputWrongNameFail()
	{
		$input = array('form_sent' => '1', 'name' => '5abra', 'surname' => 'testsurname', 'sex' => 'masculine',
		                                   'group_num' => 'grNm5', 'email' => 'email@gmail.com', 'ege_sum' => 400,
			                               'birth_year' => 1994, 'location' => 'Local');
		$this->studentValidator = new StudentValidator(new StudentMapper($this->pdo), $input);
		$datasent = $this->studentValidator->dataSent();
		$student = $this->studentValidator->checkInput($errors, $datasent);
		
		$this->assertFalse($student);
		
	}
	
	public function testCheckInputWrongSurnameFail1()
	{
		$input = array('form_sent' => '1', 'name' => 'testname', 'surname' => array('testsurname'), 'sex' => 'masculine',
		                                   'group_num' => 'grNm5', 'email' => 'email@gmail.com', 'ege_sum' => 400,
			                               'birth_year' => 1994, 'location' => 'Local');
		$this->studentValidator = new StudentValidator(new StudentMapper($this->pdo), $input);
		$datasent = $this->studentValidator->dataSent();
		$student = $this->studentValidator->checkInput($errors, $datasent);
		
		$this->assertFalse($student);
		
	}
	
	public function testCheckInputWrongSurnameFail2()
	{
		$input = array('form_sent' => '1', 'name' => 'testname', 'surname' => true, 'sex' => 'masculine',
		                                   'group_num' => 'grNm5', 'email' => 'email@gmail.com', 'ege_sum' => 400,
		                                   'birth_year' => 1994, 'location' => 'Local');
		$this->studentValidator = new StudentValidator(new StudentMapper($this->pdo), $input);
		$datasent = $this->studentValidator->dataSent();
		$student = $this->studentValidator->checkInput($errors, $datasent);
		
		$this->assertFalse($student);
		
	}
	
	public function testCheckInputWrongSexFail()
	{
		$input = array('form_sent' => '1', 'name' => 'testname', 'surname' => 'testsurname', 'sex' => 'transsexual',
		                                   'group_num' => 'grNm5', 'email' => 'email@gmail.com', 'ege_sum' => 400,
		                                   'birth_year' => 1994, 'location' => 'Local');
		$this->studentValidator = new StudentValidator(new StudentMapper($this->pdo), $input);
		$datasent = $this->studentValidator->dataSent();
		$student = $this->studentValidator->checkInput($errors, $datasent);
		
		$this->assertFalse($student);
		
	}
	
	public function testCheckInputWrongEmailFail()
	{
		$input = array('form_sent' => '1', 'name' => 'testname', 'surname' => 'testsurname', 'sex' => 'masculine',
			                               'group_num' => 'grNm5', 'email' => 'email@gmail', 'ege_sum' => 400,
		                                   'birth_year' => 1994, 'location' => 'Local');
		$this->studentValidator = new StudentValidator(new StudentMapper($this->pdo), $input);
		$datasent = $this->studentValidator->dataSent();
		$student = $this->studentValidator->checkInput($errors, $datasent);
		
		$this->assertFalse($student);
		
	}
	
	public function testCheckInputWrongEgeSumFail1()
	{
		$input = array('form_sent' => '1', 'name' => 'testname', 'surname' => 'testsurname', 'sex' => 'masculine',
		                                   'group_num' => 'grNm5', 'email' => 'email@gmail.com', 'ege_sum' => 900,
		                                   'birth_year' => 1994, 'location' => 'Local');
		$this->studentValidator = new StudentValidator(new StudentMapper($this->pdo), $input);
		$datasent = $this->studentValidator->dataSent();
		$student = $this->studentValidator->checkInput($errors, $datasent);
		
		$this->assertFalse($student);
		
	}
	
	public function testCheckInputWrongEgeSumFail2()
	{
		$input = array('form_sent' => '1', 'name' => 'testname', 'surname' => 'testsurname', 'sex' => 'masculine',
		                                   'group_num' => 'grNm5', 'email' => 'email@gmail.com', 'ege_sum' => 0,
			                               'birth_year' => 1994, 'location' => 'Local');
		$this->studentValidator = new StudentValidator(new StudentMapper($this->pdo), $input);
		$datasent = $this->studentValidator->dataSent();
		$student = $this->studentValidator->checkInput($errors, $datasent);
		
		$this->assertFalse($student);
		
	}
	
	public function testCheckInputWrongYearBirthFail1()
	{
		$input = array('form_sent' => '1', 'name' => 'testname', 'surname' => 'testsurname', 'sex' => 'masculine',
		                                   'group_num' => 'grNm5', 'email' => 'email@gmail.com', 'ege_sum' => 400,
		                                   'birth_year' => 1876, 'location' => 'Local');
		$this->studentValidator = new StudentValidator(new StudentMapper($this->pdo), $input);
		$datasent = $this->studentValidator->dataSent();
		$student = $this->studentValidator->checkInput($errors, $datasent);
		
		$this->assertFalse($student);
		
	}
	
	public function testCheckInputWrongYearBirthFail2()
	{
		$input = array('form_sent' => '1', 'name' => 'testname', 'surname' => 'testsurname', 'sex' => 'masculine',
		                                   'group_num' => 'grNm5', 'email' => 'email@gmail.com', 'ege_sum' => 400,
		                                   'birth_year' => 2280, 'location' => 'Local');
		$this->studentValidator = new StudentValidator(new StudentMapper($this->pdo), $input);
		$datasent = $this->studentValidator->dataSent();
		$student = $this->studentValidator->checkInput($errors, $datasent);
		
		$this->assertFalse($student);
		
	}
	
	public function testCheckInputWrongLocationFail()
	{
		$input = array('form_sent' => '1', 'name' => 'testname', 'surname' => 'testsurname', 'sex' => 'masculine',
			                               'group_num' => 'grNm5', 'email' => 'email@gmail.com', 'ege_sum' => 400,
		                                   'birth_year' => 1994, 'location' => 'Vietnam');
		$this->studentValidator = new StudentValidator(new StudentMapper($this->pdo), $input);
		$datasent = $this->studentValidator->dataSent();
		$student = $this->studentValidator->checkInput($errors, $datasent);
		
		$this->assertFalse($student);
		
	}
	
	public function testCheckStudent()
	{
		$test = new Student('name', 'surname', 'masculine', 'grNm5',
			                'email@gmail.com', 400, 1994, 'Local');
		$this->studentValidator = new StudentValidator( new StudentMapper($this->pdo) );
		$student = $this->studentValidator->checkStudent($test, $errors);
		
		$this->assertNotFalse($student, $errors);
		
	}
	
	public function testCheckStudentWrongNameFail()
	{
		$test = new Student('', 'surname', 'masculine', 'grNum5',
			'email@gmail.com', 400, 1994, 'Local');
		$this->studentValidator = new StudentValidator( new StudentMapper($this->pdo) );
		$student = $this->studentValidator->checkStudent($test, $errors);
		
		$this->assertFalse($student);
		
	}
	
	public function testCheckStudentWrongSurnameFail1()
	{
		$test = new Student('name1', false, 'masculine', 'grNum5',
			'email@gmail.com', 400, 1994, 'Local');
		$this->studentValidator = new StudentValidator( new StudentMapper($this->pdo) );
		$student = $this->studentValidator->checkStudent($test, $errors);
		
		$this->assertFalse($student);
		
	}
	
	public function testCheckStudentWrongSurnameFail2()
	{
		$test = new Student('name1', '5', 'masculine', 'grNum5',
			'email@gmail.com', 400, 1994, 'Local');
		$this->studentValidator = new StudentValidator( new StudentMapper($this->pdo) );
		$student = $this->studentValidator->checkStudent($test, $errors);
		
		$this->assertFalse($student);
		
	}
	
	public function testCheckStudentWrongSexFail()
	{
		$test = new Student('name1', 'gfg', 'm', 'grNum5',
			'email@gmail.com', 400, 1994, 'Local');
		$this->studentValidator = new StudentValidator( new StudentMapper($this->pdo) );
		$student = $this->studentValidator->checkStudent($test, $errors);
		
		$this->assertFalse($student);
		
	}
	
	public function testCheckStudentWrongEmailFail1()
	{
		$test = new Student('name1', 'gfg', 'masculine', 'grNum5',
			'email@gmail', 400, 1994, 'Local');
		$this->studentValidator = new StudentValidator( new StudentMapper($this->pdo) );
		$student = $this->studentValidator->checkStudent($test, $errors);
		
		$this->assertFalse($student);
		
	}
	
	public function testCheckStudentWrongEgeSumFail1()
	{
		$test = new Student('name1', 'gfg', 'masculine', 'grNum5',
			'email@gmail', 'gf', 1994, 'Local');
		$this->studentValidator = new StudentValidator( new StudentMapper($this->pdo) );
		$student = $this->studentValidator->checkStudent($test, $errors);
		
		$this->assertFalse($student);
		
	}
	
	public function testCheckStudentWrongEgeSumFail2()
	{
		$test = new Student('name1', 'gfg', 'masculine', 'grNum5',
			'email@gmail', 700, 1994, 'Local');
		$this->studentValidator = new StudentValidator( new StudentMapper($this->pdo) );
		$student = $this->studentValidator->checkStudent($test, $errors);
		
		$this->assertFalse($student);
		
	}
	
	public function testCheckStudentWrongYearBirthFail1()
	{
		$test = new Student('name1', 'gfg', 'masculine', 'grNum5',
			'email@gmail', 300, 1234, 'Local');
		$this->studentValidator = new StudentValidator( new StudentMapper($this->pdo) );
		$student = $this->studentValidator->checkStudent($test, $errors);
		
		$this->assertFalse($student);
		
	}
	
	public function testCheckStudentWrongYearBirthFail2()
	{
		$test = new Student('name1', 'gfg', 'masculine', 'grNum5',
			'email@gmail', 300, 1976, 'Local');
		$this->studentValidator = new StudentValidator( new StudentMapper($this->pdo) );
		$student = $this->studentValidator->checkStudent($test, $errors);
		
		$this->assertFalse($student);
		
	}
	
	public function testCheckStudentWrongLocationFail()
	{
		$test = new Student('name1', 'gfg', 'masculine', 'grNum5',
			'email@gmail', 300, 'abra', 'fgfg');
		$this->studentValidator = new StudentValidator( new StudentMapper($this->pdo) );
		$student = $this->studentValidator->checkStudent($test, $errors);
		
		$this->assertFalse($student);
		
	}
}
