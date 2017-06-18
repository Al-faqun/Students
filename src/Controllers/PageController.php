<?php
namespace Shinoa\StudentsList\Controllers;


use Shinoa\StudentsList\Exceptions\ControllerException;
use Shinoa\StudentsList\StatusSelector;

class PageController
{
	protected $appStatus = StatusSelector::APP_IN_DEVELOPMENT;
	protected $requests;
	protected $messages;
	
	protected function __construct()
	{
	}
	
	
	function get($key, callable $call)
	{
		$this->requests[] = new Request($_GET, $key, $call, $this);
	}
	
	function post($key, callable $call)
	{
		$this->requests[] = new Request($_POST, $key, $call, $this);
	}
	
	function cookie($key, callable $call)
	{
		$this->requests[] = new Request($_COOKIE, $key, $call, $this);
	}
	
	function noGet($key, callable $call)
	{
		$this->requests[] = new Request($_GET, $key, $call, $this, true);
	}
	
	function noPost($key, callable $call)
	{
		$this->requests[] = new Request($_POST, $key, $call, $this, true);
	}
	
	function noCookie($key, callable $call)
	{
		$this->requests[] = new Request($_COOKIE, $key, $call, $this, true);
	}
	
	function execute()
	{
		if (!empty($this->requests) AND is_array($this->requests))
		foreach ($this->requests as $request) {
			$request->call();
		}
	}
	
	function redirect($address)
	{
		header('Location: ' . $address, true, 303);
		exit();
	}
	
	/**
	 * @param int $appStatus
	 */
	public function setAppStatus(int $appStatus)
	{
		$this->appStatus = $appStatus;
	}
	
	/**
	 * @return int
	 */
	public function getAppStatus(): int
	{
		return $this->appStatus;
	}
	
	public function addMessage(String $message)
	{
		$this->messages[] = $message;
	}
	
	public function getMessages()
	{
		return $this->messages;
	}
}