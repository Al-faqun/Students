<?php
namespace Shinoa\StudentsList;
use Shinoa\StudentsList\Exceptions\StudentException;

class StudentMapper
{
	
	private $SQLBuilder;
	private $lastSTMT;
	
	private $pdo = null;


	public function __construct(\PDO $pdo)
	{
		$this->pdo = $pdo;
		$this->SQLBuilder = new StudentSQLBuilder();
	}

	public function getStudents($sortBy = 'surname', $order = 'ASC',
	                            $offset = 0, $limit = 5,
	                            $searchText = '', $searchField = '')
	{
		try {
			$students = array();
			
			$this->SQLBuilder->select();
			if ($searchText !== '') {
				$this->SQLBuilder->whereLike($searchField);
			}
			$this->SQLBuilder->orderBy($sortBy, $order);
			$this->SQLBuilder->limit($limit, $offset);
			$sql = $this->SQLBuilder->getSQL();
			
			$stmt = $this->pdo->prepare($sql);
			$values = array();
			if ($searchText !== '') {
				$values[':like'] = "%$searchText%";
			}

			if ( ($stmt->execute($values)) && ($stmt->rowCount() > 0) )  {
				while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
					$students[] = $this->convertToObject($row);
				}
			} else {
				$students = false;
			}
		} catch (\PDOException $e) {
			throw new StudentException('Ошибка при получении данных студентов', 0, $e);
		}
		$this->lastSTMT = $stmt;
		return $students;
	}

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
	
	public function lastInsertedId()
	{
		return (int)$this->pdo->lastInsertId();
	}
	
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
	
	public function findStudentByID($id)
	{
		try {
			if (!is_int($id)) {
				return false;
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
	
	public function getEntriesCount()
	{
		try {
			$this->SQLBuilder->countLastQuery();
			$sql = $this->SQLBuilder->getSQL();
			$stmt = $this->pdo->prepare($sql);
			if ($stmt->execute()) {
				if ($stmt->rowCount() == 0) {
					$count = false;
				}
				while ($row = $stmt->fetch(\PDO::FETCH_NUM)) {
					$count = $row[0];
				}
			} else {
				$count = false;
			}
		} catch (\PDOException $e) {
			throw new StudentException('Ошибка при получении числа записей студентов', 0, $e);
		}
		
		return $count;
	}
	
	public function deleteStudentByID($id)
	{
		try {
			if (!is_int($id)) {
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
			throw new StudentException('Ошибка при получении данных студента'. 0, $e);
		}

		return $result;
	}
	
	
	
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

	public function convertToStatement(Student $student, $typeOfStatement = 'insert')
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

/*
include 'Student.php';
include 'StudentSQLBuilder.phpder.php';
try {
	$opt = array(
		\PDO::ATTR_ERRMODE            => \PDO::ERRMODE_EXCEPTION,
		\PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
		\PDO::ATTR_EMULATE_PREPARES   => false
	);
	$pdo = new \PDO('mysql:host=localhost;dbname=students_book;charset=utf8', 'root', 'VtVgfhfif354', $opt);
} catch (\PDOException $e) {
	echo  $e->getCode() . PHP_EOL . $e->getMessage();
	die;
}
$SM = new StudentMapper($pdo);

$students = $SM->getStudents('name', 'DESC');
echo '<pre>';
print_r($students);
echo '<pre>';
*/

