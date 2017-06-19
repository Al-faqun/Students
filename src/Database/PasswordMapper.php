<?php
namespace Shinoa\StudentsList\Database;


use PhpParser\Node\Expr\Variable;
use Shinoa\StudentsList\Database\PassSQLBuilder;
use Shinoa\StudentsList\Exceptions\DbException;

/**
 * Class PasswordMapper
 * @package Shinoa\StudentsList
 *
 * This class is used to store, retrieve, update and delete hashes for users in specialized table of DB.
 */
class PasswordMapper
{
	/**
	 * @var \PDO
	 */
	private $pdo;
	/**
	 * Provides dynamic sql sequences.
	 * @var PassSQLBuilder
	 */
	private $sqlBuilder;
	
	/**
	 * PasswordMapper constructor.
	 * @param \PDO $pdo
	 */
	function __construct(\PDO $pdo)
	{
		$this->pdo = $pdo;
		$this->sqlBuilder = new PassSQLBuilder();
	}
	
	/**
	 * @param string|int $userId ID of user, whose hash you want to retrieve.
	 * @return string|bool Hash as string if succesful, otherwise FALSE
	 * @throws DbException
	 */
	function getHash($userId)
	{
		try {
			if ( $this->checkID($userId) != 0 ) {
				$this->sqlBuilder->select();
				$this->sqlBuilder->whereValue('userid');
				$sql = $this->sqlBuilder->getSQL();
				$stmt = $this->pdo->prepare($sql);
				$stmt->bindParam(':userid', $userId, \PDO::PARAM_INT);
				$hash = $this->getNextRowFirstEl($stmt);
			} else $hash = false;
			
		} catch (\PDOException $e) {
			throw new DbException('Ошибка при получении хешей', 0, $e);
		}
		return $hash;
	}
	
	/**
	 * Inserts into `Passwords` data with ID of user and hash.
	 *
	 * @param string|int $userId  Be careful! This method DOESN'T check whether  user with this ID exists at all!
	 * You need to check yourself that provided ID corresponds to user, that uses provided hash.
	 * @param string $hash String of possible characters, used to verify password of user.
	 * @return bool TRUE if succesfully added, else false.
	 * @throws DbException
	 */
	function addHash($userId, $hash)
	{
		try {
			if ( $this->checkID($userId) ) {
				$this->sqlBuilder->insert();
				$sql = $this->sqlBuilder->getSQL();
				
				$stmt = $this->pdo->prepare($sql);
				$stmt->bindParam(':userid', $userId, \PDO::PARAM_INT);
				$stmt->bindParam(':hash', $hash, \PDO::PARAM_STR);
				
				if ($stmt->execute()) {
					$result = true;
				} else $result = false;
			} else $result = false;
			
		} catch (\PDOException $e) {
			throw new DbException('Ошибка при получении хешей', 0, $e);
		}
		return $result;
	}
	
	/**
	 * Updates `Passwords` record of user with ID with new hash.
	 *
	 * @param string|int $userId  Be careful! This method DOESN'T check whether  user with this ID exists at all!
	 * You need to check yourself that provided ID corresponds to user, that uses provided hash.
	 * @param string $hash String of possible characters, used to verify password of user.
	 * @return bool TRUE if succesfully added, else false.
	 * @throws DbException
	 */
	function updateHash($userId, $hash)
	{
		try {
			if ( $this->checkID($userId) ) {
				$this->sqlBuilder->updateById();
				$sql = $this->sqlBuilder->getSQL();
				
				$stmt = $this->pdo->prepare($sql);
				$stmt->bindParam(':userid', $userId, \PDO::PARAM_INT);
				$stmt->bindParam(':hash', $hash, \PDO::PARAM_STR);
				
				if ($stmt->execute()) {
					$result = true;
				} else $result = false;
			} else $result = false;
			
		} catch (\PDOException $e) {
			throw new DbException('Ошибка при обновлении хеша', 0, $e);
		}
		return $result;
	}
	
	/**
	 * @param string|int $userId  Be careful! This method DOESN'T check whether  user with this ID exists at all!
	 * You need to check yourself that provided ID corresponds to user, that uses provided hash.
	 * @return bool TRUE if succesfully deleted, else FALSE.
	 * @throws DbException
	 */
	function deleteHash($userId)
	{
		try {
			$userId = $this->checkID($userId);
			if ( $userId <= 0) {
				$result = false;
			} else {
				$this->sqlBuilder->deleteByID();
				$sql = $this->sqlBuilder->getSQL();
				$stmt = $this->pdo->prepare($sql);
				$stmt->bindParam(':userid', $userId, \PDO::PARAM_INT);
				if ($stmt->execute()) {
					$result = true;
				} else $result = false;
			
			}
		} catch (\PDOException $e) {
			throw new DbException('Ошибка при удалении хеша', 0, $e);
		}
		
		return $result;
	}
	
	/**
	 * Defends against bad types casting to int.
	 *
	 * @param mixed $userId  Variable to check
	 * @return int 0 if bad type, else int value.
	 */
	private function checkID($userId)
	{
		if (is_string($userId) || is_numeric($userId)) {
			$userId = (int)$userId;
		} else $userId = 0;
		return $userId;
	}
	/**
	 * Fetches all rows (associative type) from results into enumerated array. Like rows[$i][<row like assoc array>]
	 * @param \PDOStatement $stmt Statement to be executed.
	 * @param null $values If specified, this array if used as binded params for pdo::execute. If null,  it's ignored.
	 * @return array|bool Returns numerated array, containing rows as associated arrays. On failure, returns FALSE.
	 */
	private function getAllRows(\PDOStatement &$stmt, $values = null)
	{
		$outArray = false;
		if ( ($stmt->execute($values)) && ($stmt->rowCount() > 0) ) {
			while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
				$outArray[] = $row;
			}
		} else {
				$outArray = false;
		}
		return $outArray;
	}
	
	/**
	 * Returns first element of the next row (0, 1, 2 etc).
	 * @param \PDOStatement $stmt Statement to be executed.
	 * @param null $values If specified, this array if used as binded params for pdo::execute. If null,  it's ignored.
	 * @return bool
	 */
	private function getNextRowFirstEl(\PDOStatement &$stmt, $values = null)
	{
		$element = false;
		if ( ($stmt->execute($values)) && ($stmt->rowCount() > 0) ) {
			$element = $stmt->fetchColumn();
		} else {
			$element = false;
		}
		return $element;
	}
}


