<?php
namespace Shinoa\StudentsList;


use Shinoa\StudentsList\Exceptions\RegistryException;

class Registry
{
    private static $instance;

    private $docRoot = '';
    private $conf = null;
    private $dsn = '';
    private $sortby = '';
    private $order = '';
    private $offset = '';
    private $limit = '';
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
		if (!empty($this->sortby)) {
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
		if (!empty($this->order)) {
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
		if (!empty($this->offset)) {
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
		if (!empty($this->limit)) {
			return $this->limit;
		} else throw new RegistryException('Trying to retrieve empty parameter');
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
				\PDO::ATTR_EMULATE_PREPARES   => false
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

    }


}