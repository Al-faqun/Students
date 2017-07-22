<?php
namespace Shinoa\StudentsList\Controllers;

use Shinoa\StudentsList\ErrEvoker;
use Shinoa\StudentsList\Exceptions\ControllerException;
use Shinoa\StudentsList\FileSystem;
use Shinoa\StudentsList\Loader;
use Shinoa\StudentsList\LoginManager;
use Shinoa\StudentsList\Pager;
use Shinoa\StudentsList\Database\PasswordMapper;
use Shinoa\StudentsList\SearchData;
use Shinoa\StudentsList\Input\SearchQueryValidator;
use Shinoa\StudentsList\StatusSelector;
use Shinoa\StudentsList\Database\StudentMapper;
use Shinoa\StudentsList\Views\StudentListView;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

class ListController extends PageController
{
	private $root;
	private $userID = 0;
	
	function __construct($root, $twig)
	{
		parent::__construct($twig);
		$this->root = $root;
	}
	
	function list(Request $request, Response $response, $args)
	{
		Loader::setRoot($this->root);
		$config = Loader::getConfig();
		$pdo    = Loader::getPDO();
		return $this->listPage($this->root, $pdo, $request, $response);
	}
	
	protected function listPage($root, $pdo, Request $request, Response $response)
	{
		$get    = $request->getQueryParams() ?? array();
		$cookie = $request->getCookieParams() ?? array();
		$view = new StudentListView($this->twig);
		$mapper    = new StudentMapper($pdo);
		$validator = new SearchQueryValidator($get);
		$loginMan  = new LoginManager(new PasswordMapper($pdo), $cookie);
		$pager     = new Pager();
		$messages  = array();
		
		$loginMan->checkAuth();
		$this->userID = $loginMan->getLoggedID();
		$statusText = StatusSelector::codeToText($this->getAppStatus());
		$searchData = $validator->genSearchData();
		
		$students     = $mapper->getStudents($searchData);
		$entriesCount = $mapper->getEntriesCount();
		//полное число найденных результатов для последнего поискового запроса
		$queries = $pager->getQueries($get, $entriesCount);
		
		$response = $view->output($response, array(
			'students'    => $students,
			'status_text' => $statusText,
			'messages'    => $messages,
			'queries'     => $queries
		));
		return $response;
	}
	
	public function postStatusAction(Request $request, Response $response, $args)
	{
		$post = $request->getParsedBody() ?? array();
		$response = $this->input(
			$post,
			'appStatus',
			function ($key, $value, ListController $c) use ($request, $response, $args) {
				$statusSelector = new StatusSelector();
				$code = $statusSelector->checkCode($value);
				if ($code !== false) {
					$response = $statusSelector->save($code, $request, $response);
					$response = $c->redirect('/list', $response);
				}
				return $response;
			}
		);
		return $response;
	}
	
	public function cookieStatusAction(Request $request, Response $response, $args)
	{
		$cookie = $request->getCookieParams() ?? array();
		$response = $this->input(
			$cookie,
			'appStatus',
			function ($key, $value, ListController $c) use ($request, $response, $args) {
				$statusSelector = new StatusSelector();
				$code = $statusSelector->checkCode($value);
				if ($code !== false) {
					$c->setAppStatus($code);
				}
				return $response;
			},
			$response
		);
		$response = $this->noInput(
			$cookie,
			'appStatus',
			function ($key, $value, ListController $c) use ($request, $response, $args) {
				$statusSelector = new StatusSelector();
				$code = $statusSelector->getDefaultCode(Loader::getStatus());
				$c->setAppStatus($code);
			},
			$response
		);
		return $response;
	}
	
	public function getExceptionAction(Request $request, Response $response, $args)
	{
		$get = $request->getQueryParams() ?? array();
		$response = $this->input(
			$get,
			'evokeException',
			function ($key, $value, ListController $c) use ($request, $response, $args) {
				ErrEvoker::evokeException();
			},
			$response
		);
		return $response;
	}
	
	public function getErrorAction(Request $request, Response $response, $args)
	{
		$get = $request->getQueryParams() ?? array();
		$response = $this->input(
			$get,
			'evokeError',
			function ($key, $value, ListController $c) use ($request, $response, $args) {
				ErrEvoker::evokeError();
			},
			$response
		);
		return $response;
	}
	
	
	
}