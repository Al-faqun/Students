<?php
namespace Shinoa\StudentsList\Views;


use Shinoa\StudentsList\Exceptions\StudentException;
use Shinoa\StudentsList\Exceptions\ViewException;
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
		$loader = new \Twig_Loader_Filesystem($templatesDir);
		$this->twig = new \Twig_Environment($loader, array(
			'cache' => appendFilePath([$templatesDir, 'cache']),
			'auto_reload' => false,
			'autoescape' => false
		));
	}
	
	/**
	 * Return table-body code (one row), ready for use in html. Just paste it between tbody /tbody tags.
	 *
	 * @param Student $student Object to convert into table row.
	 * @return string One table row.
	 */
	public function getTbodyHtml(Student $student)
	{
		$contents = $student->getArray();
		$tbody = '<tr>';
		foreach ($contents as $field) {
			$field = self::esc($field);
			$tbody.= "<td>{$field}</td>";
		}
		$tbody.= '</tr>';
		
		return $tbody;
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
		$tbodyContent = '';
		if ($params['students'] === false) {
			$messages[] = 'Результат: ничего не найдено.';
		} else {
			foreach ($students as $student) {
				$tbodyContent .= $this->getTbodyHtml($student);
			}
		}
		//текстовое сообщение пользователю
		$urgentMessage = $this->mesToHTML($messages);

		//загружаем шаблон, который использует вышеописанные переменные
		$template = $this->twig->load(appendFilePath(['List', 'stud_list.html.twig']));
		echo $template->render(array(
			'appStatusText' =>  $appStatusText,
			'urgentMessage' => $urgentMessage,
			'tbodyContent' => $tbodyContent,
			'queries' => $queries
		));

		return ob_get_clean();

	}
	
}