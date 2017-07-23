<?php
namespace Shinoa\StudentsList\Controllers;

use Shinoa\StudentsList\Exceptions\ControllerException;
use Shinoa\StudentsList\Exceptions\StudentException;
use Shinoa\StudentsList\FileSystem;
use Shinoa\StudentsList\Loader;
use Shinoa\StudentsList\LoginManager;
use Shinoa\StudentsList\Database\PasswordMapper;
use Shinoa\StudentsList\StatusSelector;
use Shinoa\StudentsList\Student;
use Shinoa\StudentsList\Database\StudentMapper;
use Shinoa\StudentsList\Input\StudentValidator;
use Shinoa\StudentsList\Views\RegEditView;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

class RegEditController extends PageController
{
	private $root;
	private $response;
	private $request;
	private $args;
	private $errors = array();
	public $userID = 0;
	public $loader;
	
	function __construct($root, $twig)
	{
		parent::__construct($twig);
		$this->root = $root;
	}
	
	function getRegEdit(Request $request, Response $response, $args)
	{
		Loader::setRoot($this->root);
		$config = Loader::getConfig();
		$pdo    = Loader::getPDO();
		$this->request = $request;
		$this->response = $response;
		$this->args = $args;
		return $this->getRegEditPage($pdo);
	}
	
	function postRegEdit(Request $request, Response $response, $args)
	{
		Loader::setRoot($this->root);
		$config = Loader::getConfig();
		$pdo    = Loader::getPDO();
		$this->request = $request;
		$this->response = $response;
		$this->args = $args;
		return $this->postRegEditPage($pdo);
	}
	
	private function getRegEditPage(\PDO $pdo)
	{
		$cookie = $this->request->getCookieParams() ?? array();
		$dataMapper = new StudentMapper($pdo);
		$loginMan   = new LoginManager(new PasswordMapper($pdo), $cookie);
		$loginMan->checkAuth();
		//для логирования
		$this->userID = $loginMan->getLoggedID();
		
		$isLogged = $loginMan->isLogged();
		//если юзер не залогинен - значит, он ещё не добавлял свои данные в БД
		if ($isLogged === false) {
			//отмечаем это, чтобы в дальнейшем View правильно заполнил данные в форме
			$student = false;
		//если юзер залогинен, значит, его данные уже добавлены в БД
		} elseif ($isLogged === true) {
			$student = $dataMapper->findStudentByID( $loginMan->getLoggedID() );
		}
		$view = new RegEditView($this->twig);
		$response = $view->output($this->response, array(
			'student_data' => $student,
			'is_logged'    => $isLogged,
			'messages'     => $this->messages,
			'errors'       => $this->errors
		));
		return $response;
	}
	
	private function postRegEditPage(\PDO $pdo)
	{
		$post   = $this->request->getParsedBody() ?? array();
		$cookie = $this->request->getCookieParams() ?? array();
		$dataMapper = new StudentMapper($pdo);
		$loginMan   = new LoginManager(new PasswordMapper($pdo), $cookie);
		$validator  = new StudentValidator($dataMapper, $post);
		//проверяем, залогинен ли юзер (обладает ли нужными куками)
		$loginMan->checkAuth();
		//для логирования
		$this->userID = $loginMan->getLoggedID();
		//ошибки заполнения данных студента при регистрации или обновлении
		$dataSent = $validator->dataSent();
		$isLogged = $loginMan->isLogged();
		//если юзер не залогинен - значит, он ещё не добавлял свои данные в БД
		if ($isLogged === false) {
			if ($dataSent) {
				$student = $this->processNewUser($validator, $pdo, $dataMapper, $loginMan, $post);
			} else {
				$student = false;
			}
		//если юзер залогинен, значит, его данные уже добавлены в БД
		} elseif ($isLogged === true) {
			if ($dataSent) {
				$student = $this->processExistingUser($validator, $pdo, $dataMapper, $loginMan, $post);
			} else {
				$student = $dataMapper->findStudentByID( $loginMan->getLoggedID() );
			};
		}
		$view = new RegEditView($this->twig);
		$response = $view->output($this->response, array(
			'student_data' => $student,
			'is_logged'    => $isLogged,
			'messages'     => $this->messages,
			'errors'       => $this->errors
		));
		return $response;
	}
	
	public function getUploadedAction(Request $request, Response $response, $args)
	{
		$get = $request->getQueryParams() ?? array();
		$response = $this->input(
			$get,
			'uploaded',
			function ($key, $value, RegEditController $c) use ($request, $response, $args) {
				$c->addMessage('Ваши данные успешно добавлены!');
				return $response;
			},
			$response
		);
		return $response;
	}
	
	public function getUpdatedAction(Request $request, Response $response, $args)
	{
		$get = $request->getQueryParams() ?? array();
		$response = $this->input(
			$get,
			'updated',
			function ($key, $value, RegEditController $c) use ($request, $response, $args) {
				$c->addMessage('Ваши данные успешно добавлены!');
				return $response;
			},
			$response
		);
		return $response;
	}
	
	public function cookieStatusAction(Request $request, Response $response, $args)
	{
		$cookie = $request->getCookieParams() ?? array();
		$response = $this->input(
			$cookie,
			'appStatus',
			function ($key, $value, RegEditController $c) use ($request, $response, $args) {
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
			function ($key, $value, RegEditController $c) use ($request, $response, $args) {
				$statusSelector = new StatusSelector();
				$code = $statusSelector->getDefaultCode(Loader::getStatus());
				$c->setAppStatus($code);
			},
			$response
		);
		return $response;
	}
	
	private function processNewUser(StudentValidator $validator,
	                                \PDO $pdo,
	                                StudentMapper $dataMapper,
	                                LoginManager $loginMan,
	                                $post)
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
			$studentData = false;
			$this->response = $this->redirect('/reg-edit?uploaded', $this->response);
		} else {
			//заполненный ошибками массив сохраняем для View
			$this->errors = $errors;
			//сохраняем также полученные данные студента, чтобы вернуть в форму для исправления
			$studentData = Student::makeStudentFromArray($post);
		}
		return $studentData;
	}
	
	private function processExistingUser(StudentValidator $validator,
	                                     \PDO $pdo,
	                                     StudentMapper $dataMapper,
	                                     LoginManager $loginMan,
	                                     $post)
	{
		$errors = array();
		$student = $validator->checkInput($errors, true, true);
		//данные посланы, ошибок нет - можно  передавать обновлённые данные в БД
		if (empty($errors)) {
			$dataMapper->updateStudent($student, $loginMan->getLoggedID());
			//редирект для очищения POST
			$this->response = $this->redirect('reg-edit?updated', $this->response);
			$studentData = false;
		} else {
			$this->errors = $errors;
			$studentData = Student::makeStudentFromArray($post);
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
			$response = $loginMan->logIn($insertedID, $this->request, $this->response, $this->args);
			//если нет ошибок - подтверждаем запрос в бд
			$this->response = $response;
			$pdo->commit();
		} catch (\Throwable $e) {
			//если ошибка - откатываемся и передаём наверх
			$pdo->rollBack();
			throw new ControllerException('Error commiting data to Database', 0, $e);
		}
	}
}