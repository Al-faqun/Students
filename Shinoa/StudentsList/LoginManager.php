<?php
namespace Shinoa\StudentsList;

class LoginManager
{
	private $mapper;
	private $input;
	private $islogged = false;
	
	function __construct(PasswordMapper $mapper, array $inputArray)
	{
		$this->mapper = $mapper;
		$this->input = $inputArray;
	}
	
	function logIn($userId)
	{
		$factory = new \RandomLib\Factory;
		$generator = $factory->getMediumStrengthGenerator();
		$password = $generator->generateString(32);
		$hash = password_hash($password, PASSWORD_DEFAULT);
		$this->mapper->addHash($userId, $hash);
		setcookie('pass', $password, time()+60*60*24*360, null, null, null, true);
		setcookie('userid', $userId,   time()+60*60*24*360, null, null, null, true);
	}
	
	function checkAuth()
	{
		if ( isset($input['pass']) && isset($input['userid']) )
		{
			$password = $input['pass'];
			$userId = $input['userid'];
			$hash = $this->mapper->getHash($userId);
			if ( ($userId !== false) && ($hash !== false) ) {
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
	
	function isLogged()
	{
		return $this->islogged;
	}
}