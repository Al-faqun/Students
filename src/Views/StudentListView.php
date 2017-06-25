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
	function __construct($templatesDir)
	{
		parent::__construct($templatesDir);
		$this->requiredFields = ['students',
                                 'messages',
                                 'status_text',
		                         'queries'];
		$loader = new \Twig_Loader_Filesystem(FileSystem::append([$templatesDir, 'List']));
		$this->twig = new \Twig_Environment($loader, array(
			'cache' => FileSystem::append([$templatesDir, 'cache']),
			'auto_reload' => true,
			'autoescape' => false
		));
	}
	
	/**
	 * Loads all values and preferences for a template, then loads the template into string.
	 * @var $params array Link to the params array, from which are retrieved all the data.
     * @return string html page
	 * @throws ViewException
	 */
	public function output($params)
	{
		ob_start();
		//вызывает исключение, если нет хотя бы одного аргумента
		$this->ensureParams($params);
		$students   = $params['students'];
		$appStatusText = $params['status_text'];
		$messages   = $params['messages'];
		$queries    = $params['queries'];
		//таблица со студентами
		$deletethis = $this->mesafromhtml();
		if ($params['students'] === false) {
			$messages[] = 'Результат: ничего не найдено.';
		} else {
			foreach ($students as $student) {
				$content[] = $student->getArray();
			}
			$students = $content;
		}

		//загружаем шаблон, который использует вышеописанные переменные
		$template = $this->twig->load('stud_list.html.twig');
		echo $template->render(array(
			'students'      => $students,
			'appStatusText' => $appStatusText,
			'messages'      => $messages,
			'queries'       => $queries
		));

		return ob_get_clean();

	}
	
}