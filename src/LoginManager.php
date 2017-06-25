<?php
namespace Shinoa\StudentsList;


use Shinoa\StudentsList\Database\PasswordMapper;
use Shinoa\StudentsList\Exceptions\StudentException;
use Shinoa\StudentsList\FileSystem;
class LoginManager
{
	/**
	 * @var PasswordMapper This mapper stores passwords or their hashes in DB.
	 */
	private $mapper;
	/**
	 * @var array Input, containing password and user data of current 'user' of site.
	 */
	private $input;
	/**
	 * @var bool VERY important variable, which tells outside world about,
	 * whether 'user' is logged OR it's some stranger.
	 */
	private $islogged = false;
	/**
	 * @var int Holder for id of user, if exists.
	 */
	private $id = 0;
	
	/**
	 * LoginManager constructor.
	 * @param PasswordMapper $mapper Mapper that can store hashes in DB.
	 * @param array $inputArray Input, containing 'password' and 'user data' of current user of site.
	 */
	function __construct(PasswordMapper $mapper, array $inputArray)
	{
		$this->mapper = $mapper;
		$this->input = $inputArray;
	}
	
	/**
	 * This method registers user with random password, good hash from it, and provided ID.
	 *
	 * Be careful, this method cannot check whether ID is valid and non-duplicate, so do it yourself.
	 * @param string|int $userId You need to get valid (non-duplicate) id of user.
	 */
	function logIn($userId)
	{
		$password = self::genRandString(32);
		$hash = password_hash($password, PASSWORD_DEFAULT);
		$this->mapper->addHash($userId, $hash);
		setcookie('pass',   $password, time()+60*60*24*360, null, null, null, true);
		setcookie('userid', $userId,   time()+60*60*24*360, null, null, null, true);
	}
	
	
	/**
	 * Checks depending on provided input, whether user has valid credentials
	 * and therefore may be provided with access to system.
	 *
	 * It also sets internal state of LoginManager to 'logged' or 'not logged'.
	 * @return bool TRUE if user has valid credentials and is logged, or FALSE if not.
	 */
	function checkAuth()
	{
		if ( isset($this->input['pass']) && isset($this->input['userid']) )
		{
			$password = $this->input['pass'];
			$userId   = (int)$this->input['userid'];
			$this->id = $userId;
			$hash = $this->mapper->getHash($userId);
			
			if ($hash !== false) {
				//проверка пароля
				if (password_verify($password, $hash)) {
					$this->islogged = true;
				} else {
					$this->islogged = false;
				}
				//проверка, не обновился ли стандартный способ хэширования в php
				if (password_needs_rehash($hash, PASSWORD_DEFAULT)) {
					$hash = password_hash($password, PASSWORD_DEFAULT);
					$this->mapper->updateHash($userId, $hash);
				}
			} else {
				$this->islogged = false;
			}
		} else $this->islogged = false;
		
		return $this->islogged;
	}
	
	/**
	 * Be careful, this method can only return TRUE,
	 * if you previously checked user for credentials by other methods.
	 * By default it returns FALSE.
	 * @return bool TRUE if user is valid and logged, otherwise FALSE.
	 */
	function isLogged()
	{
		return $this->islogged;
	}
	
	/**
	 * Be careful, this method can only return other than 0,
	 * if you checked user credentials by other methods.
	 * @return int ID of user, if it's credentials were checked, otherwise 0.
	 */
	function getLoggedID()
	{
		return (int)$this->id;
	}
	
	/**
	 * Generates cryptographically secure string of given length.
	 * @param int $length Length of desired random string.
	 * @param string $chars Only these characters may be included into string.
	 * @return string
	 * @throws StudentException
	 */
	private static function genRandString($length, $chars='0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ+/')
	{
		if (!is_string($chars) || strlen($chars) == 0) {
			throw new StudentException('Parameter is not string or is empty');
		}
		
		$str = '';
		$keysize = strlen($chars) -1;
		for ($i = 0; $i < $length; ++$i) {
			$str .= $chars[random_int(0, $keysize)];
		}
		return $str;
	}
}


