<?php
namespace Shinoa\StudentsList\Controllers;


use Shinoa\StudentsList\Exceptions\ControllerException;
use Shinoa\StudentsList\Loader;
use Shinoa\StudentsList\StatusSelector;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

class PageController
{
	protected $twig;
	protected $appStatus = StatusSelector::APP_IN_DEVELOPMENT;
	protected $requests;
	protected $messages;
	
	protected function __construct($twig)
	{
		$this->twig = $twig;
	}
	
	function input($input, $key, callable $call, $default = false)
	{
		if (array_key_exists($key, $input)) {
			return ($call)($key, $input[$key], $this);
		} else return $default;
	}
	
	function noInput($input, $key, callable $call, $default = false)
	{
		if (!array_key_exists($key, $input)) {
			return ($call)($key, null, $this);
		} else return $default;
	}
	
	function redirect($address, Response $response)
	{
		$response = $response->withHeader('Location', $address);
		return $response;
	}
	
	/**
	 * @param int $appStatus
	 */
	public function setAppStatus(int $appStatus)
	{
		$this->appStatus = $appStatus;
		Loader::setStatus($appStatus);
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