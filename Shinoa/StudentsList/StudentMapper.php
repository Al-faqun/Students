<?php
namespace Shinoa\StudentsList;
use Shinoa\StudentsList\Exceptions\StudentException;

class StudentMapper
{
	const SQL_SELECT_ALL =
		'SELECT
		 students.id,
		 students.name,
		 students.surname,
		 students.sex,
		 students.group_num,
		 students.email,
		 students.ege_sum,
		 students.birth_year,
		 students.location
		 FROM
		 students_book.students
		 ORDER BY :sort_by, :order
		 LIMIT :offset, :limit';

	const  SQL_INSERT_STUDENT =
		'INSERT INTO `students`
	    (`name`,       `surname`, `sex`, 
	     `group_num`,  `email`,   `ege_sum`, 
	     `birth_year`, `location`)
	    VALUES
	    (:name, :surname, :sex, 
	     :group_num, :email, :ege_sum, 
	     :birth_year, :location)';

	const SQL_SELECT_ALL_BY_ID =
		'SELECT
		 students.id,
		 students.name,
		 students.surname,
		 students.sex,
		 students.group_num,
		 students.email,
		 students.ege_sum,
		 students.birth_year,
		 students.location
		 FROM
		 students_book.students
		 WHERE `id` = :id
		 LIMIT 1';

	const SQL_DELETE_BY_ID =
		'DELETE FROM `students` WHERE `id` = :id';

	public $pdo;

	public function __construct(\PDO $pdo)
	{
		$this->pdo = $pdo;
	}

	private static function backticks($name)
	{
		$result = "`" .str_replace("`", "``", $name) . "`";
		return $result;
	}

	public function getStudents($sortBy = 'surname', $order = 'ASC', $offset = 0, $limit = 5)
	{
		try {
			$students = array();
			$stmt = $this->pdo->prepare(self::SQL_SELECT_ALL);

			$sortBy = self::backticks($sortBy);
			$values = array(':sort_by' => $sortBy, ':order' => $order,
				            ':offset'  => $offset,  ':limit' => $limit);

			if ($stmt->execute($values)) {
				while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
					$students[] = $this->convertToObject($row);
				}
			} else {
				$students = false;
			}
		} catch (\PDOException $e) {
			print $e->getMessage();
			throw new StudentException('Ошибка при получении данных студентов', 0, $e);
		}

		return $students;
	}

	public function insertStudent(Student $student)
	{
		try {
			$stmt = $this->convertToStatement($student);
			$result = $stmt->execute();
		} catch (\PDOException $e) {
			throw new StudentException('Ошибка при добавлении данных студента', 0, $e);
		}

		return $result;
	}

	public function findStudentByID($id)
	{
		try {
			if (!is_int($id)) {
				return false;
			}

			$stmt = $this->pdo->prepare(self::SQL_SELECT_ALL_BY_ID);
			$stmt->bindParam(':id', $id, \PDO::PARAM_INT);

			if ($stmt->execute()) {
				$row = $stmt->fetch(\PDO::FETCH_ASSOC);
				if ( $row ) {
					$student = $this->convertToObject($row);
				} else $student = false;
			} else $student = false;

		} catch (\PDOException $e) {
			throw new StudentException('Ошибка при получении данных студента', 0, $e);
		}

		return $student;
	}

	public function deleteStudentByID($id)
	{
		try {
			if (!is_int($id)) {
				return false;
			}

			$stmt = $this->pdo->prepare(self::SQL_DELETE_BY_ID);
			$stmt->bindParam(':id', $id, \PDO::PARAM_INT);
			if ($stmt->execute()) {
				$result = true;
			} else $result = false;
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

	public function convertToStatement(Student $student)
	{
		try {

			$stmt = $this->pdo->prepare(self::SQL_INSERT_STUDENT);
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

/*try {
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
echo '<pre>';
try {
	var_dump($stmt = $SM->pdo->prepare(StudentMapper::SQL_INSERT_STUDENT));
} catch (\PDOException $e) {
	echo  $e->getCode() . PHP_EOL . $e->getMessage();
}
echo '</pre>';*/