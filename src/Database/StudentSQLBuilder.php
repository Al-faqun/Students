<?php
namespace Shinoa\StudentsList\Database;



use Shinoa\StudentsList\Exceptions\StudentException;
use Zend\Cache\Exception\LogicException;

/**
 * Class StudentSQLBuilder
 * @package Shinoa\StudentsList
 * Use this class to dynamically create sql with `students` table.
 */
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
		  students' . self::space;
	
	const INSERT_STUDENT =
		'INSERT
		 INTO `students`
		 (`name`,       `surname`, `sex`,
	      `group_num`,  `email`,   `ege_sum`,
	      `birth_year`, `location`)
	     VALUES
	     (:name,       :surname, :sex,
	      :group_num,  :email,   :ege_sum,
	      :birth_year, :location)' . self::space;
	
	const UPDATE_STUDENT =
		'UPDATE `students`
		 SET
		 `name`  = :name,  `surname`   = :surname,
		 `sex`   = :sex,   `group_num` = :group_num,
		 `email` = :email, `ege_sum`   = :ege_sum,
		 `birth_year` = :birth_year,
		 `location`   = :location
		 WHERE `id` = :id' . self::space;
	
	const DELETE_BY_ID =
		'DELETE FROM `students` WHERE `id` = :id';
	
	const SELECT_DUPLICATE =
		'SELECT COUNT(*)
	     FROM students' . self::space;
	
	const SQL_COUNT_ROWS =
		'SELECT FOUND_ROWS()';
	
	/**
	 * @var string
	 * Holds previously constructed sql.
	 * Must be constructed by public methods.
	 */
	private $sql = '';
	
	public function __construct()
	{
	}
	
	/**
	 * Quotes given string with backticks (` sign).
	 * @param string $name
	 * @return string Quoted with backticks result
	 */
	private static function backticks($name)
	{
		$result = "`" .str_replace("`", "``", $name) . "`";
		return $result;
	}
	
	/**
	 * Returns currently constructed sql.
	 * @return string
	 */
	public function getSQL()
	{
		return $this->sql;
	}
	
	/**
	 * Adds to sql WHERE clause with LIKE keyword;
	 * Use :like placeholder to bind variable to query.
	 * @param string $column
	 */
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
	
	/**
	 * Adds to sql WHERE clause, i.e. "WHERE columnName = columnValue";
	 * Use :name, where 'name' is the name of your column, as placeholder to bind variable to query.
	 * @param string $column
	 */
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
	
	/**
	 * Adds ORDER BY clase with provided column name and direction of sotring
	 * @param string $column name of column to sort by
	 * @param string $order either 'ASC' or 'DESC', caseinsensitive
	 */
	public function orderBy($column, $order)
	{
		$column = self::backticks($column);
		if (preg_match('/ORDER BY/ui', $this->sql) == 0) {
			$this->sql .= "ORDER BY $column $order" . self::space;
		} else {
			$this->sql .= ", ($column $order)" . self::space;
		}
	}
	
	/**
	 * Adds LIMIT clause starting with $offset and containing $limit rows
	 * @param int $limit Number of rows that must be affected with query
	 * @param int $offset Number of row, from which starts the counting
	 */
	public function limit($limit, $offset)
	{
		$limit = (int)$limit;
		$offset = (int)$offset;
		if (preg_match('/LIMIT/ui', $this->sql) == 0) {
			$this->sql .= "LIMIT $limit" . self::space;
			if ($offset !== '') {
				$this->sql .= "OFFSET $offset" . self::space;
			}
		} else {
			throw new \LogicException('LIMIT keyword cannot be used twice');
		}
	}
	
	/**
	 * Sets internal sql to basic SELECT query.
	 */
	public function select()
	{
		$this->sql = self::SELECT_BASE;
	}
	
	/**
	 * Sets internal sql to query for counting number of rows,
	 * that COULD be affected by last select query !without! additional clauses.
	 */
	public function countLastQuery()
	{
		$this->sql = self::SQL_COUNT_ROWS;
	}
	
	/**
	 * Sets internal sql to  INSERT query.
	 */
	public function insert()
	{
		$this->sql = self::INSERT_STUDENT;
	}
	
	/**
	 * Sets internal sql to UPDATE query with :id placeholder.
	 */
	public function updateById()
	{
		$this->sql = self::UPDATE_STUDENT;
	}
	
	/**
	 * Sets internal sql to DELETE query with :id placeholder.
	 */
	public function deleteByID()
	{
		$this->sql = self::DELETE_BY_ID;
	}
	
	/**
	 * Sets internal sql to SELECT COUNT(*) query.
	 * You need to use this with WHERE clause to get meaningful result.
	 */
	public function selectDuplicate()
	{
		$this->sql = self::SELECT_DUPLICATE;
	}
}