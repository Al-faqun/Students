<?php
namespace Shinoa\StudentsList;


use Shinoa\StudentsList\Exceptions\RegistryException;

class Registry
{
    private static $instance;

    private $docRoot = '';
    private $conf = null;
    private $dsn = '';
    private $view = null;
    private $dataProcessor = null;

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

    private function autoload($className)
    {
        $className = ltrim($className, '\\');
        $fileName = '';
        $namespace = '';
        if ($lastNsPos = strrpos($className, '\\')) {
            $namespace = substr($className, 0, $lastNsPos);
            $className = substr($className, $lastNsPos + 1);
            $fileName = str_replace('\\', DIRECTORY_SEPARATOR, $namespace) . DIRECTORY_SEPARATOR;
        }
        $fileName .= str_replace('_', DIRECTORY_SEPARATOR, $className) . '.php';

        $path = $this->docRoot . DIRECTORY_SEPARATOR . 'Students' . DIRECTORY_SEPARATOR . $fileName;
	    if (file_exists($path)) {
		    require $path;
	    }
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
        	$this->dsn = "mysql:host=localhost;dbname={$this->conf['database']['dbname']};charset=utf8";
        }
        return $this->dsn;
    }

	private function loadDatabase()
	{
		try {
			$opt = array(
				\PDO::ATTR_ERRMODE            => \PDO::ERRMODE_EXCEPTION,
				\PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
				\PDO::ATTR_EMULATE_PREPARES   => false
			);
			$this->pdo = new \PDO($this->dsn, $this->conf['username'], $this->conf['password'], $opt);
		} catch (\PDOException $e) {
			throw new RegistryException('Ошибка при подключении к базе данных'. 0, $e);
		}
    }

    public function init($docRoot, $pathToConf, $view)
    {
	    spl_autoload_register(array($this, 'autoload'));
        $this->setRoot($docRoot);
        $this->setConfig($pathToConf);

    }


}