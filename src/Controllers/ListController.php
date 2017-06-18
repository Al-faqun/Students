<?php
namespace Shinoa\StudentsList\Controllers;


use Shinoa\StudentsList\CommonView;
use Shinoa\StudentsList\ErrorHelper;
use Shinoa\StudentsList\Exceptions\ControllerException;
use Shinoa\StudentsList\Exceptions\StudentException;
use Shinoa\StudentsList\Loader;
use Shinoa\StudentsList\LoginManager;
use Shinoa\StudentsList\Pager;
use Shinoa\StudentsList\PasswordMapper;
use Shinoa\StudentsList\SearchData;
use Shinoa\StudentsList\SearchQueryValidator;
use Shinoa\StudentsList\StatusSelector;
use Shinoa\StudentsList\StudentMapper;
use Shinoa\StudentsList\StudentView;

class ListController extends PageController
{
	public $loader;
	private $root;
	private $userID = 0;
	
	function __construct()
	{
		parent::__construct();
	}
	
	function start($root)
	{
		try {
			Loader::setRoot($root);
			$config = Loader::getConfig();
			$pdo    = Loader::getPDO();
			$this->execute();
			$this->listPage($root, $pdo, $config);
			
		} catch (\Throwable $e) {
			//класс, заведующий обработкой ошибок
			$errorHelper = new ErrorHelper( appendFilePath([$root, 'templates']) );
			//предпринимаем действия, в зависимости от режима приложения: 'в разработке' или 'в производстве'
			switch ($this->appStatus) {
				case StatusSelector::APP_IN_DEVELOPMENT:
					$errorHelper->renderExceptionAndExit($e, '/');
					break;
				
				case StatusSelector::APP_IN_PRODUCTION:
					$userMes = 'Encountered error, logs are sent to developer. Please, try again later!';
					//форматируем текст для записи в лог-файл
					$text = ErrorHelper::errorToArray($e);
					array_unshift($text, date('d-M-Y H:i:s') . ' ');
					$text[] = 'UserID = ' . $this->userID;
					$logpath = appendFilePath( [$root, 'public', 'errors.log'] );
					$errorHelper->addToLog($text, $logpath);
					$errorHelper->renderErrorPageAndExit($userMes, '/');
					break;
			}
		}
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
		
		$entriesCount = $mapper->getEntriesCount();
		$students     = $mapper->getStudents($searchData);
		//полное число найденных результатов для последнего поискового запроса
		$queries = $pager->getQueries($_GET, $entriesCount);
		
		$view = new StudentView( appendFilePath([$root, 'templates']) );
		$view->render(['students'    => $students,
		               'status_text' => $statusText,
		               'messages'    => $messages,
		               'queries'     => $queries]);
	}
	
	
	
	
}