<?php
namespace Shinoa\StudentsList;



use Shinoa\StudentsList\Exceptions\StudentException;
use Zend\Cache\Exception\LogicException;

class StudentSQLBuilder
{
	const space = ' ';
	const SELECT_BASE =
		'SELECT
	      SQL_CALC_FOUND_ROWS
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
		  students_book.students' . self::space;
	
	
	const INSERT_STUDENT =
		'INSERT
		 INTO `students`
		 (`name`,       `surname`, `sex`,
	      `group_num`,  `email`,   `ege_sum`,
	      `birth_year`, `location`)
	     VALUES
	     (:name,       :surname, :sex,
	      :group_num,  :email,   :ege_sum,
	      :birth_year, :location)';
	
	const UPDATE_STUDENT =
		'UPDATE `students`
		 SET
		 `name`  = :name,  `surname`   = :surname,
		 `sex`   = :sex,   `group_num` = :group_num,
		 `email` = :email, `ege_sum`   = :ege_sum,
		 `birth_year` = :birth_year,
		 `location`   = :location
		 WHERE `id` = :id';
	
	const DELETE_BY_ID =
		'DELETE FROM `students` WHERE `id` = :id';
	
	const SQL_COUNT_ROWS =
		'SELECT FOUND_ROWS()';
	
	private $sql = '';
	
	public function __construct()
	{
	}
	
	private static function backticks($name)
	{
		$result = "`" .str_replace("`", "``", $name) . "`";
		return $result;
	}
	
	public function getSQL()
	{
		return $this->sql;
	}
	
	public function whereLike($column)
	{
		$placeholder = ':like';
		$column = self::backticks($column);
		if (preg_match('/WHERE/ui', $this->sql) == 0) {
			$this->sql .= "WHERE ($column LIKE $placeholder)" . self::space;
		} else {
			if (preg_match("/$placeholder/ui", $this->sql) !== 0) {
				throw new \LogicException('Placeholder cannot be used twice');
			}
			$this->sql .= "AND ($column LIKE %$placeholder%)" . self::space;
		}
	}
	
	public function whereValue($column)
	{
		$placeholder = ":$column";
		$column = self::backticks($column);
		if (preg_match('/WHERE/ui', $this->sql) == 0) {
			$this->sql .= "WHERE ($column = $placeholder)" . self::space;
		} else {
			if (preg_match("/$placeholder/ui", $this->sql) !== 0) {
				throw new \LogicException('Placeholder cannot be used twice');
			}
			$this->sql .= "AND ($column = $placeholder)" . self::space;
		}
	}
	
	public function orderBy($column, $order)
	{
		$column = self::backticks($column);
		if (preg_match('/ORDER BY/ui', $this->sql) == 0) {
			$this->sql .= "ORDER BY $column $order" . self::space;
		} else {
			$this->sql .= ", ($column $order)" . self::space;
		}
	}
	
	public function limit($limit, $offset = '')
	{
		if (preg_match('/LIMIT/ui', $this->sql) == 0) {
			$this->sql .= "LIMIT $limit" . self::space;
			if ($offset !== '') {
				$this->sql .= "OFFSET $offset" . self::space;
			}
		} else {
			throw new \LogicException('LIMIT keyword cannot be used twice');
		}
	}
	
	public function select()
	{
		$this->sql = self::SELECT_BASE;
	}
	
	public function countLastQuery()
	{
		$this->sql = self::SQL_COUNT_ROWS;
	}
	
	public function insert()
	{
		$this->sql = self::INSERT_STUDENT;
	}
	
	public function updateById()
	{
		$this->sql = self::UPDATE_STUDENT;
	}
	
	public function deleteByID()
	{
		$this->sql = self::DELETE_BY_ID;
	}
}

/*
$SQL = new StudentSQLBuilder();
echo $SQL->selectAll('testsearchfield', 'sortfield', 'DESC') . '<br>';
echo $SQL->selectAllLimit('testsearchfield', 'sortfield', 'DESC', 10, 5) . '<br>';
echo $SQL->selectAllLimit('testsearchfield', 'sortfield', 'DESC', 10) . '<br>';
echo $SQL->selectAllByID() . '<br>';
echo $SQL->insert() . '<br>';
echo $SQL->deleteByID() . '<br>';
*/
