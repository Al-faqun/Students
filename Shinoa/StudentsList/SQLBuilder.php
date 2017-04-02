<?php
namespace Shinoa\StudentsList;



use Shinoa\StudentsList\Exceptions\StudentException;
use Zend\Cache\Exception\LogicException;

class SQLBuilder
{
	const space = ' ';
	const SELECT_BASE =
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
	
	const DELETE_BY_ID =
		'DELETE FROM `students` WHERE `id` = :id';
	
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
				throw new LogicException('Placeholder cannot be used twice');
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
				throw new LogicException('Placeholder cannot be used twice');
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
			throw new LogicException('LIMIT keyword cannot be used twice');
		}
	}
	
	public function select()
	{
		$this->sql = self::SELECT_BASE;
	}
	
	/*public function selectAll($sortBy, $order)
	{
		$this->select();
		$this->orderBy($sortBy, $order);
		return $this->sql;
	}
	
	public function selectAllSearch($searchField, $sortBy, $order)
	{
		$this->select();
		$this->whereLike($searchField);
		$this->orderBy($sortBy, $order);
		return $this->sql;
	}
	
	public function selectAllLimit($searchField, $sortBy, $order, $limit, $offset = '')
	{
		$this->select();
		$this->whereLike($searchField);
		$this->orderBy($sortBy, $order);
		$this->limit($limit, $offset);
		return $this->sql;
	}
	
	public function selectAllByID()
	{
		$this->select();
		$this->whereValue('id');
		$this->limit(1);
		return $this->sql;
	} */
	
	public function insert()
	{
		$this->sql = self::INSERT_STUDENT;
		return $this->sql;
	}
	
	public function deleteByID()
	{
		$this->sql = self::DELETE_BY_ID;
		return $this->sql;
	}
}

/*
$SQL = new SQLBuilder();
echo $SQL->selectAll('testsearchfield', 'sortfield', 'DESC') . '<br>';
echo $SQL->selectAllLimit('testsearchfield', 'sortfield', 'DESC', 10, 5) . '<br>';
echo $SQL->selectAllLimit('testsearchfield', 'sortfield', 'DESC', 10) . '<br>';
echo $SQL->selectAllByID() . '<br>';
echo $SQL->insert() . '<br>';
echo $SQL->deleteByID() . '<br>';
*/