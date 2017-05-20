<?php
namespace Shinoa\StudentsList;


use Shinoa\StudentsList\Exceptions\ViewException;

/**
 * Class StudentView is used to construct and show to user the page: list of students
 * @package Shinoa\StudentsList
 */
class StudentView extends CommonView
{
	/**
	 * How many records must be displayed per page.
	 */
	const STUDENTS_IN_PAGE = 20;
	
	/**
	 * StudentView constructor.
	 * @param Registry $registry
	 * @param string $templatesDir
	 */
	function __construct(Registry $registry, $templatesDir)
	{
		parent::__construct($registry, $templatesDir);
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
	 * Fetches current get parameter and produces query for them. Just append it to url with '?'.
	 * @param int $page
	 * @return string
	 */
	public function getPaginationQuery($page = 0)
	{
		if (is_int($page)) {
			$_GET['page'] = $page;
		} else throw new \InvalidArgumentException('Parameter is not int.');
		
		ksort($_GET);
		
		$query = http_build_query($_GET);
		
		return $query;
	}
	
	/**
	 * Loads all values and preferences for a template, then loads the template into string.
	 * @todo Yeah, I know its nasty code, with no standard and reusability. Gotcha do smth with that.
	 * @return string
	 * @throws ViewException
	 */
	public function output()
	{
		//в конце мы сохраним всю страницу в строку
		ob_start();
		
		//главный маппер - выдаёт список студентов
		$this->dataMapper = $this->registry->getDataMapper();
		$students = $this->dataMapper->getStudents($this->registry->getSortby(),
		                                           $this->registry->getOrder(),
		                                           $this->registry->getOffset(),
			                                       $this->registry->getLimit(),
		                                           $this->registry->getSearchText(),
		                                           $this->registry->getSearchField()
		);
		
		//таблица со студентами
		$tbodyContent = '';
		//различные текстовые сообщения пользователю
		$urgentMessage = '';
		$messages = $this->registry->getMessages();
		if ($students === false) {
			$messages[] = 'Результат: ничего не найдено.';
		} else {
			foreach ($students as $student) {
				$tbodyContent .= $this->getTbodyHtml($student);
			}
		}
		//эта переменная идёт в шаблон
		$urgentMessage = $this->mesToHTML($messages);
		
		//текст для показа пользователю текущего режима программы
		$appStatusText = $this->registry->getStatusText();
		
		//пажинация
		//полное число найденных результатов для последнего поискового запроса (getStudents)
		$entriesCount = $this->dataMapper->getEntriesCount();
		//число страниц, на которых можно отобразить все  результаты
		$pageCount = ceil($entriesCount / self::STUDENTS_IN_PAGE);
		//для каждой из страниц создаём оригинальную ссылку
		for ($i = 1; $i <= $pageCount; $i++) {
			$queries[$i] = $this->getPaginationQuery($i);
		}

		//загружаем шаблон, который использует вышеописанные переменные
		//обратите внимание! в начале шаблона прописаны все необходимые для его работы перменные.
		// Если хоть одна из них не установлена, приложение сваливается с исключением!
		$filepath = $this->templatesDir . '/tpl_stud_list.php';
		if (file_exists($filepath)) {
			include $filepath;
		} else throw new ViewException('File doesnt exist.');

		return ob_get_clean();

	}
	
}