<?php
namespace Shinoa\StudentsList\Database;
use Shinoa\StudentsList\Exceptions\StudentException;
use InvalidArgumentException;
use Shinoa\StudentsList\InvalidTypeException;
use Shinoa\StudentsList\SearchData;
use Shinoa\StudentsList\Student;
use Shinoa\StudentsList\Database\StudentSQLBuilder;

/**
 * Class StudentMapper.
 * Allowes handy retrieving, inserting, updating, deleting of Students with `Student` table of DB.
 *
 * Attention! This mapper uses provided data 'as is' and do NOT perform any checks of data's sanity.
 *
 * Use dedicated data validators before passing any input to this class!
 * @package Shinoa\StudentsList
 */
class StudentMapper
{
	/**
	 * @var StudentSQLBuilder
	 * Provides dynamic sql sequences.
	 */
	private $SQLBuilder;
	
	private $lastCount = 0;
	
	/**
	 * @var \PDO
	 */
	private $pdo;
	
	/**
	 * StudentMapper constructor.
	 * @param \PDO $pdo
	 */
	public function __construct(\PDO $pdo)
	{
		$this->pdo = $pdo;
		$this->SQLBuilder = new StudentSQLBuilder();
	}
	
	
	/**
	 * Fetch a number of students from database.
	 * @param SearchData $data
	 * @return array|bool Array of class Student or FALSE on failure.
	 * @throws StudentException
	 */
	public function getStudents(SearchData $data)
	{
		$text   = $data->getSearchText();    $field  = $data->getSearchField();
		$sortBy = $data->getSortby();        $order  = $data->getOrder();
		$limit  = $data->getLimit();         $offset = $data->getOffset();
		try {
			$students = array();
			
			$this->SQLBuilder->select();
			if ($text !== '') {
				$this->SQLBuilder->whereLike($field);
			}
			$this->SQLBuilder->orderBy($sortBy, $order);
			$this->SQLBuilder->limit($limit, $offset);
			$sql = $this->SQLBuilder->getSQL();
			
			$stmt = $this->pdo->prepare($sql);
			$values = array();
			if ($text !== '') {
				$values[':like'] = "%$text%";
			}

			if ( ($stmt->execute($values)) && ($stmt->rowCount() > 0) )  {
				while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
					$students[] = $this->convertToObject($row);
				}
			} else {
				$students = false;
			}
			
			$this->lastCount = $this->foundRows();
		} catch (\PDOException $e) {
			throw new StudentException('Ошибка при получении данных студентов', 0, $e);
		}
		return $students;
	}
	
	/**
	 * Adds student data to  database.
	 * @param Student $student
	 * @return bool TRUE on succesful retrieval from DB, FALSE on failure
	 * @throws StudentException
	 */
	public function insertStudent(Student $student)
	{
		try {
			$stmt = $this->convertToStatement($student, 'insert');
			$result = $stmt->execute();
		} catch (\PDOException $e) {
			throw new StudentException('Ошибка при добавлении данных студента', 0, $e);
		}

		return $result;
	}
	
	/**
	 * @return int id of last inserted ID or 0 if cannot retrieve
	 */
	public function lastInsertedId()
	{
		return (int)$this->pdo->lastInsertId();
	}
	
	/**
	 * Changes data of chosen Student in database by id.
	 * @param Student $student
	 * @param $id
	 * @return bool true if successfully updated student data, or false
	 * @throws StudentException
	 */
	public function updateStudent(Student $student, $id)
	{
		try {
			$stmt = $this->convertToStatement($student, 'update');
			$stmt->bindParam(':id', $id, \PDO::PARAM_INT);
			$result = $stmt->execute();
		} catch (\PDOException $e) {
			throw new StudentException('Ошибка при обновлении данных студента', 0, $e);
		}
		
		return $result;
	}
	
	/**
	 * Retrieves student from database by it's ID.
	 * @param string|int $id String or integer
	 * @return bool|Student Student on success, else FALSE
	 * @throws StudentException|InvalidTypeException
	 */
	public function findStudentByID($id)
	{
		try {
			if ( (int)$id <= 0 ) {
				throw new \UnexpectedValueException('Параметр должен быть больше нуля');
			}
			
			$this->SQLBuilder->select();
			$this->SQLBuilder->whereValue('id');
			$sql = $this->SQLBuilder->getSQL();
			$stmt = $this->pdo->prepare($sql);
			$stmt->bindParam(':id', $id, \PDO::PARAM_INT);

			if ($stmt->execute()) {
				$row = $stmt->fetch(\PDO::FETCH_ASSOC);
				if ( !empty($row) ) {
					$student = $this->convertToObject($row);
				} else $student = false;
			} else $student = false;

		} catch (\PDOException $e) {
			throw new StudentException('Ошибка при получении данных студента', 0, $e);
		}

		return $student;
	}
	
	/**
	 * Checks database for duplicates of a specific column with a specific value
	 * @param string $columnName Name of column in database
	 * @param mixed $columnValue Value of the named column, which will be checked for duplicates
	 * @return bool TRUE if value already exists in that column, otherwise FALSE
	 * @throws StudentException
	 */
	public function existsValue($columnName, $columnValue)
	{
		try {
			$result = false;
			$this->SQLBuilder->selectDuplicate();
			$this->SQLBuilder->whereValue($columnName);
			$sql = $this->SQLBuilder->getSQL();
			$stmt = $this->pdo->prepare($sql);
			
			if (is_int($columnValue)) {
				$stmt->bindParam($columnName, $columnValue, \PDO::PARAM_INT);
			} else {
				$stmt->bindParam($columnName, $columnValue, \PDO::PARAM_STR);
			}
			
			if ( $stmt->execute() ) {
				$row = $stmt->fetch(\PDO::FETCH_NUM);
				if ( !empty($row) && $row[0] > 0) {
					$result = true;
				} else $result = false;
			} else $result = false;
			
		} catch (\PDOException $e) {
			throw new StudentException('Ошибка при получении данных студента', 0, $e);
		}
		return $result;
	}
	
	/**
	 * Gets number of rows, affected by last select query, !without! WHERE clauses.
	 * @return mixed|bool number of rows on success, FALSE if failure.
	 * @throws StudentException
	 */
	public function getEntriesCount()
	{
		return $this->lastCount;
	}
	
	public function foundRows()
	{
		try {
			//initialize default value of result
			$count = false;
			//get sql
			$this->SQLBuilder->countLastQuery();
			$sql = $this->SQLBuilder->getSQL();
			$stmt = $this->pdo->prepare($sql);
			if ($stmt->execute()) {
				//if get nothing from DB
				if ($stmt->rowCount() == 0) {
					$count = false;
				}
				$row = $stmt->fetch(\PDO::FETCH_NUM);
				//on success we get only one item from DB
				$count = $row[0];
				
			} else {
				$count = false;
			}
		} catch (\PDOException $e) {
			throw new StudentException('Ошибка при получении числа записей студентов', 0, $e);
		}
		
		return $count;
	}
	
	/**
	 * Deletes student from database by it's ID
	 * @param mixed $id ID of student to delete from database
	 * @return bool true if successfully deleted, otherwise false
	 * @throws StudentException
	 */
	public function deleteStudentByID($id)
	{
		try {
			$id = (int)$id;
			if ( $id <= 0) {
				$result = false;
			} else {
				$this->SQLBuilder->deleteByID();
				$sql = $this->SQLBuilder->getSQL();
				$stmt = $this->pdo->prepare($sql);
				$stmt->bindParam(':id', $id, \PDO::PARAM_INT);
				if ($stmt->execute()) {
					$result = true;
				} else $result = false;
			}
		} catch (\PDOException $e) {
			throw new StudentException('Ошибка при удалении данных студента', 0, $e);
		}

		return $result;
	}
	
	/**
	 * Converts array with student data into Student object.
	 * @param array $row Array that contains items,
	 * named after Student class public fields.
	 * @return Student
	 * @throws StudentException
	 */
	private function convertToObject($row)
	{
		$required = array('name' => 1, 'surname' => 2, 'sex' => 3, 'group_num' => 4,
			'email' => 5, 'ege_sum' => 6, 'birth_year' => 7, 'location' => 8);
		if (( !is_array($row) )  || ( !empty(array_diff_key($required, $row)) )) {
			throw new StudentException('Строка не содержит нужных данных');
		}

		$student = new Student($row['name'],       $row['surname'],    $row['sex'],
		                       $row['group_num'],  $row['email'], (int)$row['ege_sum'],
			              (int)$row['birth_year'], $row['location']);

		return $student;

	}
	
	/**
	 * @param Student $student
	 * @param string $typeOfStatement either 'insert' or 'update' (case insensitive)
	 * @return \PDOStatement If 'insert', needs only to execute; if 'update', separetely bind :id
	 * @throws StudentException
	 */
	private function convertToStatement(Student $student, $typeOfStatement = 'insert')
	{
		try {
			switch ( strtolower($typeOfStatement) ) {
				case 'insert':
					$this->SQLBuilder->insert();
					break;
				case 'update':
					$this->SQLBuilder->updateById();
					break;
				default:
					throw new StudentException('Incorrect type of statement');
			}
			$sql = $this->SQLBuilder->getSQL();
			$stmt = $this->pdo->prepare($sql);
			
			$name = $student->getName();
			$surname = $student->getSurname();
			$sex = $student->getSex();
			$groupNum = $student->getGroupNum();
			$email = $student->getEmail();
			$egeSum = $student->getEgeSum();
			$yearOfBirth = $student->getYearOfBirth();
			$location = $student->getLocation();

			$stmt->bindParam(':name', $name);
			$stmt->bindParam(':surname', $surname);
			$stmt->bindParam(':sex', $sex);
			$stmt->bindParam(':group_num', $groupNum);
			$stmt->bindParam(':email', $email);
			$stmt->bindParam(':ege_sum', $egeSum, \PDO::PARAM_INT);
			$stmt->bindParam(':birth_year', $yearOfBirth, \PDO::PARAM_INT);
			$stmt->bindParam(':location', $location);
		} catch (\PDOException $e) {
			throw new StudentException('Ошибка при добавлении данных студента', 0, $e);
		}
		return $stmt;
	}
}
