<?php
namespace Shinoa\StudentsList;

class LoginManager
{
	private $mapper;
	private $input;
	private $islogged = false;
	private $id = 0;
	
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
		
		if ( isset($this->input['pass']) && isset($this->input['userid']) )
		{
			$password = $this->input['pass'];
			$userId = (int)$this->input['userid'];
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
	
	function isLogged()
	{
		return $this->islogged;
	}
	
	function getLoggedID()
	{
		return (int)$this->id;
	}
}