<?php
namespace Shinoa\StudentsList\Database;

class PassSQLBuilder
{
	const space = ' ';
	
	const SELECT =
		'SELECT
	     SQL_CALC_FOUND_ROWS
		 <field_names>
		 FROM <table_name>' . self::space;
	
	
	const INSERT =
		'INSERT
		 INTO <table_name>
		 SET <columns_set_expressions>' . self::space;
	
	const UPDATE =
		'UPDATE <table_name>
		 SET <columns_set_expressions>
		 WHERE `userid` = :userid' . self::space;
	
	const DELETE_BY_ID =
		'DELETE FROM <table_name> WHERE `userid` = :userid' . self::space;
	
	const SQL_COUNT_ROWS =
		'SELECT FOUND_ROWS()' . self::space;
	
	private $tableName  = '';
	private $fieldNames = '';
	private $insertSet = '';
	private $updateSet = '';
	private $sql = '';
	
	function __construct()
	{
		$this->tableName = self::backticks('passwords');
		$this->fieldNames = self::fieldSet( ['hash'] );
		$this->insertSet = self::pdoSet( ['userid', 'hash'] );
		$this->updateSet = self::pdoSet( ['hash'] );
		
	}
	
	private static function fieldSet($fieldNames)
	{
		$set = '';
		foreach ($fieldNames as $field) {
			$set .= self::backticks( str_replace("`","``",$field) ) . ", ";
		}
		return substr($set, 0, -2);
	}
	
	private static function pdoSet($fieldNames) {
		$set = '';
		foreach ($fieldNames as $field) {
			$set .= self::backticks(str_replace("`","``",$field)) . " = :$field, ";
		}
		return substr($set, 0, -2);
	}
	
	private static function backticks($name)
	{
		$result = "`" .str_replace("`", "``", $name) . "`";
		return $result;
	}
	
	private static function spaces($string)
	{
		$result = self::space .$string . self::space;
		return $result;
	}
	
	public function getSQL()
	{
		return $this->sql;
	}
	
	function select()
	{
		$search = ['<field_names>', '<table_name>'];
		$replace = [self::spaces($this->fieldNames), self::spaces($this->tableName)];
		$this->sql = str_replace($search, $replace, self::SELECT);
	}
	
	function countLastQuery()
	{
		$this->sql = self::SQL_COUNT_ROWS;
	}
	
	function insert()
	{
		$search = ['<table_name>', '<columns_set_expressions>'];
		$replace = [self::spaces($this->tableName), self::spaces($this->insertSet)];
		$this->sql = str_replace($search, $replace, self::INSERT);
	}
	
	function updateById()
	{
		$search = ['<table_name>', '<columns_set_expressions>'];
		$replace = [self::spaces($this->tableName), self::spaces($this->updateSet)];
		$this->sql = str_replace($search, $replace, self::UPDATE);
	}
	
	function deleteByID()
	{
		$search = ['<table_name>'];
		$replace = [self::spaces($this->tableName)];
		$this->sql = str_replace($search, $replace, self::DELETE_BY_ID);
	}
	
	function whereLike($column)
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
	
	function whereValue($column)
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
	
	function orderBy($column, $order)
	{
		$column = self::backticks($column);
		if (preg_match('/ORDER BY/ui', $this->sql) == 0) {
			$this->sql .= "ORDER BY $column $order" . self::space;
		} else {
			$this->sql .= ", ($column $order)" . self::space;
		}
	}
	
	function limit($limit, $offset = '')
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
	
}

