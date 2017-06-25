<?php
namespace Shinoa\StudentsList\Controllers;

use Shinoa\StudentsList\ErrorHelper;
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

class ListController extends PageController
{
	private $root;
	private $userID = 0;
	
	function __construct()
	{
		parent::__construct();
	}
	
	function start($root)
	{
		Loader::setRoot($root);
		$config = Loader::getConfig();
		$pdo    = Loader::getPDO();
		$this->setAppStatus(Loader::getStatus());
		$this->execute();
		$this->listPage($root, $pdo, $config);
	}
	
	protected function listPage($root, $pdo, $config)
	{
		$mapper         = new StudentMapper($pdo);
		$validator      = new SearchQueryValidator($_GET);
		$statusSelector = new StatusSelector();
		$loginMan = new LoginManager(new PasswordMapper($pdo), $_COOKIE);
		$pager    = new Pager();
		$messages = array();
		
		$loginMan->checkAuth();
		$this->userID = $loginMan->getLoggedID();
		$statusText = StatusSelector::codeToText($this->appStatus);
		$searchData = $validator->genSearchData();
		
		$students     = $mapper->getStudents($searchData);
		$entriesCount = $mapper->getEntriesCount();
		//полное число найденных результатов для последнего поискового запроса
		$queries = $pager->getQueries($_GET, $entriesCount);
		$view = new StudentListView( FileSystem::append([Loader::getRoot(), 'templates']) );
		$view->render(['students'    => $students,
		               'status_text' => $statusText,
		               'messages'    => $messages,
		               'queries'     => $queries]);
	}
	
	
	
	
}