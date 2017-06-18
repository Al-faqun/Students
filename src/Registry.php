<?php
namespace Shinoa\StudentsList;


use Shinoa\StudentsList\Exceptions\LoaderException;

/**
 * This class forms the link between various parts of application,
 * like userinput checkout, database interaction, and output of result to enduser.
 * This class isn't pretty, and I'm going to do something about it.
 *
 * @package Shinoa\StudentsList
 */
class Registry
{
	//режимы работы приложения
	const APP_IN_DEVELOPMENT = 0;
	const APP_IN_PRODUCTION  = 1;
	
	//единственный экземпляр класса
    private static $instance;
	
    private $docRoot;        //файловый путь к корневой папке (например, localhost/)
    private $conf;           //массив с данными конфигурации из файла
    private $dsn;            //характеристики соединения с БД
    private $status = 0;     //режим работы приложения
    
	
    
    //общие для программы классы
    private $studentData;
	private $pdo;
	private $dataMapper ;
	private $view;
	private $loginManager;
	
	//id user'a (если есть) для нужд логов ошибок
	private $userID = 0;
	
	//различные переменные для отображения данных пользователю
	//обычные сообщения, например, об успехе операции
    private $messages = array();
    //сообщения об ошибках, которые пользователь должен исправить
    private $errors = array();
    //текстовое сообщение о режиме приложения
    private $statusText = '';
	
	/**
	 * Registry constructor.
	 */
    private function __construct()
    {
    }
	
	/**
	 * The only way to get instance of Registry class.
	 *
	 * @return Registry
	 */
    public static function getInstance()
    {
        if (!isset(self::$instance)) {
                   self::$instance = new self();
        }
        return self::$instance;
    }
	
	/**
	 * Loads config from file.
	 *
	 * @param string $pathToConf Full path to the file (.xml)
	 * @throws LoaderException
	 */
	private function setConfig($pathToConf)
	{
		if ( file_exists($pathToConf) ) {
			$this->conf = simplexml_load_file($pathToConf);
		} else throw new LoaderException('Given path is not file');
	}
	
	/**
	 * Loads database connection parameters from preloaded config.
	 * Attention! Config must be loaded from file before execution of this method.
	 * Be ware, this method does not establish connection with DB.
	 *
	 * @return string DSN
	 * @throws LoaderException
	 */
	public function getDSN()
	{
		if ( empty($this->conf) ) {
			throw new LoaderException('Configuration is not properly loaded');
		}
		
		if ( empty($this->dsn) ) {
			$this->dsn = "mysql:host=localhost;dbname={$this->conf->database->dbname};charset=utf8";
		}
		return $this->dsn;
	}
	
	/**
	 * Provides the only instance of PDO avaible in the application.
	 *
	 * @return \PDO
	 * @throws LoaderException
	 */
	public function getPDO()
	{
		//если объект PDO не создан - создаём, иначе возвращаем готовый
		if ( !isset($this->pdo) ) {
			//загружаем данные для соединения
			if (!isset($this->conf->database->username)
				||
				!isset($this->conf->database->password)
			) {
				throw new LoaderException('Config  not loaded or empty');
			} else {
				$username = $this->conf->database->username;
				$password = $this->conf->database->password;
			}
			//пробуем соединиться
			try {
				$opt = array(
					\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
					\PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
					\PDO::ATTR_EMULATE_PREPARES => false,
					\PDO::MYSQL_ATTR_FOUND_ROWS => true
				);
				$this->pdo = new \PDO($this->getDSN(), $username, $password, $opt);
			} catch (\PDOException $e) {
				throw new LoaderException('Ошибка при подключении к базе данных');
			}
		}
		return $this->pdo;
		
	}
	
	/**
	 * Performs basic initialization of class.
	 * Call this method before first use of the class!
	 *
	 * @param string $docRoot Full filepath to root folder (like, .../localhost/)
	 * @param string $pathToConf Full filepath to configuration file (i.e., xml)
	 */
	public function init($docRoot, $pathToConf)
	{
		$this->setRoot($docRoot);
		$this->setConfig($pathToConf);
		$this->setStatus();
		
	}
	
	/**
	 * Changes app status to provided parameter, or loads it from config.
	 *
	 * @param int|null $status Optional status of application (i.e. 0, 1)
	 * @throws LoaderException
	 */
	public function setStatus($status = null)
	{
		//если статус не указан -
		if ( $status === null ) {
			//грузим из конфига
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
						throw new LoaderException('App status is not properly loaded');
				}
			} else throw new LoaderException('App status is not properly loaded');
		} else {
			$this->status = $status;
		}
    }
	
	/**
	 * Sets default status (from config).
	 *
	 * @throws LoaderException
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
					throw new LoaderException('App status is not properly loaded');
			}
		} else throw new LoaderException('App status is not properly loaded');
	}
	
	/**
	 * Returns current status of application (i.e. in development, in production).
	 *
	 * @return int Current status
	 * @throws LoaderException
	 */
	public function getStatus()
	{
		if (isset($this->status)) {
			return $this->status;
		} else throw new LoaderException('App status is not properly loaded');
			
    }
	
	/**
	 * Set  text representation of application status.
	 * Note, this is independent from aplication status itself.
	 *
	 * @param string $statusText
	 */
	public function setStatusText(string $statusText)
	{
		$this->statusText = $statusText;
	}
	
	/**
	 * Returns text representation of application status.
	 * Note, this is independent from aplication status itself.
	 *
	 * @return string
	 */
	public function getStatusText()
	{
		return $this->statusText;
	}
	
	/**
	 * Provided full filepath to document root will be used everywhere thoughout the application.
	 * Document root is the primal folder of the site, usually one step higher than the project folder;
	 * i.e. s1.localhost/Students - filepath, correcponding to s1.localhost, is document root.
	 * @param string $documentRoot  Full filepath to document root of the site.
	 * Be careful, this is not project's base folder!
	 * @throws LoaderException
	 */
	private function setRoot($documentRoot)
	{
		if ( is_dir($documentRoot) ) {
			$this->docRoot = $documentRoot;
		} else throw new LoaderException('Given path is not directory');
    }
	
	/**
	 * Returns full filepath to document root of the site.
	 * @return string
	 * @throws LoaderException
	 */
    public function getRoot()
    {
        if ( isset($this->docRoot) ) {
	        return $this->docRoot;
        } else throw new LoaderException('Incorrect root to folder');
    }
	
	/**
	 * Memorizes reference to view object.
	 * @param null $view
	 */
	public function setView($view)
	{
		$this->view = $view;
	}
	
	/**
	 * Returns reference to the view object, if any, else null
	 * @return object|null
	 * @throws LoaderException
	 */
	public function getView()
	{
		if (!empty($this->view)) {
			return $this->view;
		} else throw new LoaderException('Trying to retrieve empty parameter');
	}
	
	/**
	 * Memorizes reference to database access object.
	 *
	 * @param  $dataMapper
	 */
	public function setDataMapper($dataMapper)
	{
		$this->dataMapper = $dataMapper;
	}
	
	/**
	 * Returns reference to database access
	 * @return mixed
	 * @throws LoaderException
	 */
	public function getDataMapper()
	{
		if (!empty($this->dataMapper)) {
			return $this->dataMapper;
		} else throw new LoaderException('Trying to retrieve empty parameter');
	}
	
	/**
	 * Memorizes reference to the object that provides login-logout capabilities.
	 *
	 * @param LoginManager $loginManager
	 */
	public function setLoginManager(LoginManager $loginManager)
	{
		$this->loginManager = $loginManager;
	}
	
	/**
	 * Returns reference to the object that provides login-logout capabilities.
	 * @return LoginManager
	 * @throws LoaderException
	 */
	public function getLoginManager()
	{
		if (!empty($this->loginManager)) {
			return $this->loginManager;
		} else throw new LoaderException('Trying to retrieve empty parameter');
	}
	
	/**
	 * Saves partially complete (or validated) Student data.
	 * Technically that's the same Student object, but it's noted that it's not fully validated.
	 *
	 * @param Student $studentData
	 */
	public function saveStudentData(Student $studentData)
	{
		$this->studentData = $studentData;
	}
	
	/**
	 * Returns partially complete (or validated) Student data.
	 *
	 * @return Student
	 */
	public function getStudentData()
	{
		return $this->studentData;
	}
	
	/**
	 * Returns currently logged person's profile from the Database.
	 *
	 * @return Student
	 */
	public function getCurrentStudentFromDB()
	{
		$student = $this->dataMapper->findStudentByID( $this->loginManager->getLoggedID() );
		return $student;
	}
	
	
	
	/**
	 * Adds provided messag to storage.
	 * These messages are used for communication with siteuser.
	 *
	 * @param string $message
	 */
	public function addMessage($message)
	{
		$this->messages[] = $message;
	}
	
	/**
	 * Returns all the messages for user.
	 *
	 * @return array
	 */
	public function getMessages()
	{
		return $this->messages;
	}
	
	/**
	 * Replaces errors array in storage with provided array.
	 * This array must consist of strings about mistakes, allowed by siteuser.
	 *
	 * @param array $errors
	 * @throws LoaderException
	 */
	public function setErrors($errors)
	{
		if (is_array($errors)) {
			$this->errors = $errors;
		} else throw new LoaderException('Setting parameter of incompatible type');
	}
	
	/**
	 * Returns array of error messages.
	 * This array consist of strings about mistakes, allowed by siteuser.
	 * @return array
	 */
	public function getErrors()
	{
		return $this->errors;
	}
	
	/**
	 * Returns TRUE if current user is logged, FALSE if not.
	 *
	 * @return boolean
	 */
	public function isLogged()
	{
		return $this->loginManager->isLogged();
	}
	
	/**
	 * Memorizes ID of logged user.
	 * @param int $userID
	 */
	public function setUserID(int $userID)
	{
		$this->userID = $userID;
	}
	
	/**
	 * Returns ID of logged user (if exists), or 0 if isn't logged.
	 * @return int
	 */
	public function getUserID(): int
	{
		return $this->userID;
	}
}