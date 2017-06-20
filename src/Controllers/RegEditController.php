<?php
namespace Shinoa\StudentsList\Controllers;

use Shinoa\StudentsList\ErrorHelper;
use Shinoa\StudentsList\Exceptions\ControllerException;
use Shinoa\StudentsList\Exceptions\StudentException;
use Shinoa\StudentsList\Loader;
use Shinoa\StudentsList\LoginManager;
use Shinoa\StudentsList\Database\PasswordMapper;
use Shinoa\StudentsList\StatusSelector;
use Shinoa\StudentsList\Student;
use Shinoa\StudentsList\Database\StudentMapper;
use Shinoa\StudentsList\Input\StudentValidator;
use Shinoa\StudentsList\Views\RegEditView;

class RegEditController extends PageController
{
	private $errors;
	public $userID = 0;
	public $loader;
	
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
			$this->regEditPage($pdo);
			
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
	
	function regEditPage(\PDO $pdo)
	{
		$dataMapper = new StudentMapper($pdo);
		$loginMan   = new LoginManager(new PasswordMapper($pdo), $_COOKIE);
		$validator  = new StudentValidator($dataMapper, $_POST);
		$student = false;
		//ошибки заполнения данных студента при регистрации или обновлении
		$dataSent = $validator->dataSent();
		$loginMan->checkAuth();
		//для логирования
		$this->userID = $loginMan->getLoggedID();
		
		$isLogged = $loginMan->isLogged();
		//если юзер не залогинен - значит, он ещё не добавлял свои данные в БД
		if ($isLogged === false) {
			if ($dataSent) {
				$student = $this->processNewUser($validator, $pdo, $dataMapper, $loginMan);
			} else {
				$student = false;
			}
		//если юзер залогинен, значит, его данные уже добавлены в БД
		} elseif ($isLogged === true) {
			if ($dataSent) {
				$student = $this->processExistingUser($validator, $pdo, $dataMapper, $loginMan);
			} else {
				$student = $dataMapper->findStudentByID( $loginMan->getLoggedID() );
			};
		}
		$view = new RegEditView(appendFilePath([Loader::getRoot(), '/templates']));
		$view->render(['student_data' => $student,
		               'is_logged'    => $isLogged,
		               'messages'     => $this->messages,
		               'errors'       => $this->errors]);
	}
	
	private function processNewUser(StudentValidator $validator, \PDO $pdo,
	                                StudentMapper    $dataMapper, LoginManager $loginMan)
	{
		//если данные вообще были посланы -
		//только тогда выполняем какие-либо манипуляции
		//если данные формы заполнены верно, возвращает class Student, во всех остальных случаях - false
		$errors = array();
		$student = $validator->checkInput($errors, true, false);
		//данные посланы, ошибок нет - можно добавлять в БД
		if (empty($errors)) {
			$this->addNewUser($student, $pdo, $dataMapper, $loginMan);
			//редирект для очищения POST
			$this->redirect('reg-edit.php?uploaded');
			$studentData = false;
		} else {
			//заполненный ошибками массив сохраняем для View
			$this->errors = $errors;
			//сохраняем также полученные данные студента, чтобы вернуть в форму для исправления
			$studentData = Student::makeStudentFromArray($_POST);
		}
		return $studentData;
	}
	
	private function processExistingUser(StudentValidator $validator, \PDO $pdo,
	                                     StudentMapper    $dataMapper, LoginManager $loginMan)
	{
		$errors = array();
		$student = $validator->checkInput($errors, true, true);
		//данные посланы, ошибок нет - можно  передавать обновлённые данные в БД
		if (empty($errors)) {
			$dataMapper->updateStudent($student, $loginMan->getLoggedID());
			//редирект для очищения POST
			$this->redirect('reg-edit.php?updated');
			$studentData = false;
		} else {
			$this->errors = $errors;
			$studentData = Student::makeStudentFromArray($_POST);
		}
		return $studentData;
	}
	
	private function addNewUser(Student $student,
	                            \PDO $pdo,
	                            StudentMapper $dataMapper,
	                            LoginManager $loginMan)
	{
		try {
			//поскольку у нас две взаимосвязанных запроса - открываем транзакцию
			$pdo->beginTransaction();
			$dataMapper->insertStudent($student);
			$insertedID = $dataMapper->lastInsertedId();
			$loginMan->logIn($insertedID);
			//если нет ошибок - подтверждаем запрос в бд
			$pdo->commit();
		} catch (\Throwable $e) {
			//если ошибка - откатываемся и передаём наверх
			$pdo->rollBack();
			throw new ControllerException('Error commiting data to Database', 0, $e);
		}
	}
}