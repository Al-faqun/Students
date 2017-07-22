<?php
namespace Shinoa\StudentsList\Views;


use Shinoa\StudentsList\Exceptions\StudentException;
use Shinoa\StudentsList\Exceptions\ViewException;
use Shinoa\StudentsList\FileSystem;
use Shinoa\StudentsList\Student;

/**
 * Class StudentListView is used to construct and show to user the page: list of students
 * @package Shinoa\StudentsList
 */
class StudentListView extends CommonView
{
	/**
	 * StudentListView constructor.
	 * @param string $templatesDir
	 */
	function __construct($twig)
	{
		parent::__construct($twig);
		$this->requiredFields = ['students',
                                 'messages',
                                 'status_text',
		                         'queries'];
		$this->twig = $twig;
	}
	
	/**
	 * Loads all values and preferences for a template, then loads the template into string.
	 * @var \Psr\Http\Message\ResponseInterface $response
	 * @var $params array Link to the params array, from which are retrieved all the data.
     * @return string html page
	 * @throws ViewException
	 */
	public function output(\Psr\Http\Message\ResponseInterface $response, $params)
	{
		//вызывает исключение, если нет хотя бы одного аргумента
		$this->ensureParams($params);
		$students      = $params['students'];
		$appStatusText = $params['status_text'];
		$messages   = $params['messages'];
		$queries    = $params['queries'];
		if ($params['students'] === false) {
			$messages[] = 'Результат: ничего не найдено.';
		} else {
			foreach ($students as $student) {
				$content[] = $student->getArray();
			}
			$students = $content;
		}
		
		//загружаем шаблон, который использует вышеописанные переменные
		$response = $this->twig->render($response, 'stud_list.html.twig', array(
			'students'      => $students,
			'appStatusText' => $appStatusText,
			'messages'      => $messages,
			'queries'       => $queries
		));

		return $response;
	}
	
}