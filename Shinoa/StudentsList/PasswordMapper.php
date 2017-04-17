<?php
namespace Shinoa\StudentsList;


use Shinoa\StudentsList\Exceptions\DbException;

class PasswordMapper
{
	private $pdo;
	private $sqlBuilder;
	
	function __construct(\PDO $pdo)
	{
		$this->pdo = $pdo;
		$this->sqlBuilder = new PassSQLBuilder();
	}
	
	function getHash($userId)
	{
		try {
			$this->sqlBuilder->select();
			$this->sqlBuilder->whereValue('userid');
			$sql = $this->sqlBuilder->getSQL();
			
			$stmt = $this->pdo->prepare($sql);
			$stmt->bindParam(':userid', $userId, \PDO::PARAM_INT);
			$hash = $this->getNextRow($stmt);
		} catch (\PDOException $e) {
			throw new DbException('Ошибка при получении хешей', 0, $e);
		}
		return $hash;
	}
	
	function addHash($userId, $hash)
	{
		try {
			$this->sqlBuilder->insert();
			$sql = $this->sqlBuilder->getSQL();
			
			$stmt = $this->pdo->prepare($sql);
			$stmt->bindParam(':userid', $userId, \PDO::PARAM_INT);
			$stmt->bindParam(':hash', $hash, \PDO::PARAM_STR);
			
			if ($stmt->execute()) {
				$result = true;
			} else $result = false;
		} catch (\PDOException $e) {
			throw new DbException('Ошибка при получении хешей', 0, $e);
		}
		return $result;
	}
	
	function updateHash($hash, $userId)
	{
		try {
			$this->sqlBuilder->updateById();
			$sql = $this->sqlBuilder->getSQL();
			
			$stmt = $this->pdo->prepare($sql);
			$stmt->bindParam(':userid', $userId, \PDO::PARAM_INT);
			$stmt->bindParam(':hash', $hash, \PDO::PARAM_STR);
			
			if ($stmt->execute()) {
				$result = true;
			} else $result = false;
		} catch (\PDOException $e) {
			throw new DbException('Ошибка при обновлении хеша', 0, $e);
		}
		return $result;
	}
	
	private function getAllRows(\PDOStatement &$stmt, $values = null)
	{
		$outArray = false;
		if ( ($stmt->execute($values)) && ($stmt->rowCount() > 0) ) {
			while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
				$outArray[] = $row[0];
			}
		} else {
				$outArray = false;
		}
		return $outArray;
	}
	
	private function getNextRow(\PDOStatement &$stmt, $values = null)
	{
		$outArray = false;
		if ( ($stmt->execute($values)) && ($stmt->rowCount() > 0) ) {
			$row = $stmt->fetch(\PDO::FETCH_ASSOC);
			$outArray[] = $row[0];
		} else {
			$outArray = false;
		}
		return $outArray;
	}
}


include 'PassSQLBuilder.php';
include 'D:\USR\apache\htdocs\s1.localhost\Students\Shinoa\StudentsList\Exceptions\DbException.php';
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
$mapper = new PasswordMapper($pdo);
var_dump($mapper->updateHash(3, 'updated hash'));


