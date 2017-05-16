<?php
namespace Shinoa\StudentsList;


use Shinoa\StudentsList\Exceptions\RegistryException;

class Registry
{
	const APP_IN_DEVELOPMENT = 0;
	const APP_IN_PRODUCTION = 1;
	
    private static $instance;

    private $docRoot;
    private $conf;
    private $dsn;
    private $status = 0;
    
    private $searchText;
    private $searchField;
    private $sortby;
    private $order;
    private $offset;
    private $limit;
    
    private $studentData;
    private $messages = array();
    private $errors = array();
    private $statusText = '';
    private $entriesCount = 0;
    private $dataMapper ;
	private $view;
	private $loginManager;

    private function __construct()
    {
    }

    public static function getInstance()
    {
        if (!isset(self::$instance)) {
                   self::$instance = new self();
        }
        return self::$instance;
    }
	
	public function setStatus($status = null)
	{
		if ( $status === null ) {
			if (isset($this->conf->app->status)) {
				$status = $this->conf->app->status;
				switch ($status) {
					case 0:
					case 'APP_IN_DEVELOPMENT':
						$this->status = APP_IN_DEVELOPMENT;
						break;
					case 1:
					case 'APP_IN_PRODUCTION':
						$this->status = APP_IN_PRODUCTION;
						break;
					default:
						throw new RegistryException('App status is not properly loaded');
				}
			} else throw new RegistryException('App status is not properly loaded');
		} else {
			$this->status = $status;
		}
    }
	
	/**
	 * @return mixed
	 */
	public function statusUseDefault()
	{
		if (isset($this->conf->app->status)) {
			$status = $this->conf->app->status;
			switch ($status) {
				case 'APP_IN_DEVELOPMENT':
					$this->status = APP_IN_DEVELOPMENT;
					break;
				case 'APP_IN_PRODUCTION':
					$this->status = APP_IN_PRODUCTION;
					break;
				default:
					throw new RegistryException('App status is not properly loaded');
			}
		} else throw new RegistryException('App status is not properly loaded');
	}
	
	public function getStatus()
	{
		if (isset($this->status)) {
			return $this->status;
		} else throw new RegistryException('App status is not properly loaded');
			
    }
	
	/**
	 * @param string $statusText
	 */
	public function setStatusText(string $statusText)
	{
		$this->statusText = $statusText;
	}
	
	/**
	 * @return string
	 */
	public function getStatusText()
	{
		return $this->statusText;
	}
	
	private function setRoot($documentRoot)
	{
		if ( is_dir($documentRoot) ) {
			$this->docRoot = $documentRoot;
		} else throw new RegistryException('Given path is not directory');
    }

    public function getRoot()
    {
        if ( isset($this->docRoot) ) {
	        return $this->docRoot;
        } else throw new RegistryException('Incorrect root to folder');
    }
	
	/**
	 * @param string $searchText
	 */
	public function setSearchText($searchText)
	{
		$this->searchText = $searchText;
	}
	
	/**
	 * @return string
	 */
	public function getSearchText()
	{
		if ($this->searchText !== null) {
			return $this->searchText;
		} else throw new RegistryException('Trying to retrieve empty parameter');
	}
	
	/**
	 * @param string $searchField
	 */
	public function setSearchField($searchField)
	{
		$this->searchField = $searchField;
	}
	
	/**
	 * @return string
	 */
	public function getSearchField()
	{
		if ($this->searchField !== null) {
			return $this->searchField;
		} else throw new RegistryException('Trying to retrieve empty parameter');
		
	}
	
	
	/**
	 * @param string $sortby
	 */
	public function setSortby($sortby)
	{
		$this->sortby = $sortby;
	}

	/**
	 * @return string
	 */
	public function getSortby()
	{
		if ($this->sortby !== null) {
			return $this->sortby;
		} else throw new RegistryException('Trying to retrieve empty parameter');
	}

	/**
	 * @param string $order
	 */
	public function setOrder($order)
	{
		$this->order = $order;
	}

	/**
	 * @return string
	 */
	public function getOrder()
	{
		if ($this->order !== null) {
			return $this->order;
		} else throw new RegistryException('Trying to retrieve empty parameter');
	}

	/**
	 * @param string $offset
	 */
	public function setOffset($offset)
	{
		$this->offset = $offset;
	}

	/**
	 * @return string
	 */
	public function getOffset()
	{
		if ($this->offset !== null) {
			return $this->offset;
		} else throw new RegistryException('Trying to retrieve empty parameter');
	}

	/**
	 * @param string $limit
	 */
	public function setLimit($limit)
	{
		$this->limit = $limit;
	}

	/**
	 * @return string
	 */
	public function getLimit()
	{
		if ($this->limit !== null) {
			return $this->limit;
		} else throw new RegistryException('Trying to retrieve empty parameter');
	}
	
	public function getEntriesCount()
	{
	
	}
	
	public function setEntriesCount()
	{
	
	}
	
	public function addMessage($message)
	{
		$this->messages[] = $message;
	}
	
	public function getMessages()
	{
		return $this->messages;
	}
	
	public function setErrors($errors)
	{
		if (is_array($errors)) {
			$this->errors = $errors;
		} else throw new RegistryException('Setting parameter of incompatible type');
	}
	
	public function getErrors()
	{
		return $this->errors;
	}
	
	
	public function isLogged()
	{
		return $this->loginManager->isLogged();
	}
	
	
	/**
	 * @param null $view
	 */
	public function setView($view)
	{
		$this->view = $view;
	}

	/**
	 * @return null
	 */
	public function getView()
	{
		if (!empty($this->view)) {
			return $this->view;
		} else throw new RegistryException('Trying to retrieve empty parameter');
	}

	/**
	 * @param null $dataMapper
	 */
	public function setDataMapper($dataMapper)
	{
		$this->dataMapper = $dataMapper;
	}

	/**
	 * @return null
	 */
	public function getDataMapper()
	{
		if (!empty($this->dataMapper)) {
			return $this->dataMapper;
		} else throw new RegistryException('Trying to retrieve empty parameter');
	}
	
	/**
	 * @param mixed $loginManager
	 */
	public function setLoginManager(LoginManager $loginManager)
	{
		$this->loginManager = $loginManager;
	}
	
	/**
	 * @return mixed
	 */
	public function getLoginManager()
	{
		if (!empty($this->view)) {
			return $this->loginManager;
		} else throw new RegistryException('Trying to retrieve empty parameter');
	}
	
	public function saveStudentData(Student $studentData)
	{
		$this->studentData = $studentData;
	}
	
	public function getStudentData()
	{
		return $this->studentData;
	}
	
	public function getCurrentStudentFromDB()
	{
		$student = $this->dataMapper->findStudentByID( $this->loginManager->getLoggedID() );
		return $student;
	}
	
	private function setConfig($pathToConf)
	{
		if ( file_exists($pathToConf) ) {
			$this->conf = simplexml_load_file($pathToConf);
		} else throw new RegistryException('Given path is not file');
	}
	
	public function getDSN()
	{
		if ( empty($this->conf) ) {
			throw new RegistryException('Configuration is not properly loaded');
		}
		
		if ( empty($this->dsn) ) {
			$this->dsn = "mysql:host=localhost;dbname={$this->conf->database->dbname};charset=utf8";
		}
		return $this->dsn;
	}
	
	public function getPDO()
	{
		if (!isset($this->conf->database->username)
			||
			!isset($this->conf->database->password)) {
			throw new RegistryException('Config  not loaded or empty');
		} else {
			$username = $this->conf->database->username;
			$password = $this->conf->database->password;
		}
		try {
			$opt = array(
				\PDO::ATTR_ERRMODE            => \PDO::ERRMODE_EXCEPTION,
				\PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
				\PDO::ATTR_EMULATE_PREPARES   => false,
				\PDO::MYSQL_ATTR_FOUND_ROWS   => true
			);
			$pdo = new \PDO($this->getDSN(), $username, $password, $opt);
			return $pdo;

		} catch (\PDOException $e) {
			throw new RegistryException('Ошибка при подключении к базе данных', 0, $e);
		}

    }

    public function init($docRoot, $pathToConf)
    {
        $this->setRoot($docRoot);
        $this->setConfig($pathToConf);
        $this->setStatus();
        

    }


}