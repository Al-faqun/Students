<?php
namespace Shinoa\StudentsList;


use Shinoa\StudentsList\Exceptions\StudentException;
use Shinoa\StudentsList\Exceptions\ViewException;

/**
 * Class StudentView is used to construct and show to user the page: list of students
 * @package Shinoa\StudentsList
 */
class StudentView extends CommonView
{
	/**
	 * StudentView constructor.
	 * @param string $templatesDir
	 */
	function __construct($templatesDir)
	{
		parent::__construct($templatesDir);
		$this->requiredFields = ['students',
                                 'messages',
                                 'status_text',
		                         'queries'];
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
		$statusText = $params['status_text'];
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
		//текст для показа пользователю текущего режима программы
		$appStatusText = $statusText;

		//загружаем шаблон, который использует вышеописанные переменные
		//обратите внимание! в начале шаблона прописаны все необходимые для его работы перменные.
		// Если хоть одна из них не установлена, приложение сваливается с исключением!
		$filepath = appendFilePath( [$this->templatesDir, 'stud_list.php'] ) ;
		if (file_exists($filepath)) {
			require $filepath;
		} else throw new ViewException('File doesnt exist.');

		return ob_get_clean();

	}
	
}