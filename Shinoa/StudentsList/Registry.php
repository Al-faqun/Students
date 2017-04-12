<?php
namespace Shinoa\StudentsList;


use Shinoa\StudentsList\Exceptions\RegistryException;

class Registry
{
	const APP_IN_DEVELOPMENT = 0;
	const APP_IN_PRODUCTION = 1;
	
    private static $instance;

    private $docRoot = null;
    private $conf = null;
    private $dsn = null;
    private $status = null;
    
    private $searchText = null;
    private $searchField = null;
    private $sortby = null;
    private $order = null;
    private $offset = null;
    private $limit = null;
    
    private $messages = array();
    private $entriesCount = 0;
    private $dataMapper = null;
	private $view = null;


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
	
	public function setStatus()
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
		}
		else throw new RegistryException('App status is not properly loaded');
    }
    
	public function getStatus()
	{
		if (isset($this->status)) {
			return $this->status;
		} else throw new RegistryException('App status is not properly loaded');
			
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
	
	public function setMessages($messages)
	{
		if (is_array($messages)) {
			$this->messages = $messages;
		} else throw new RegistryException('Setting parameter of incompatible type');
	}
	
	public function getMessages()
	{
		return $this->messages;
	}
	
	public function getEntriesCount()
	{
	
	}
	
	public function setEntriesCount()
	{
	
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
        //switch ($this->conf->app->status);

    }


}