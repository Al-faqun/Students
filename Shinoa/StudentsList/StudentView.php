<?php
namespace Shinoa\StudentsList;


use Shinoa\StudentsList\Exceptions\ViewException;

class StudentView
{
	const STUDENTS_IN_PAGE = 20;
	private $registry;
	private $dataMapper;
	private $templatesDir = '';

	function __construct(Registry $registry, $templatesDir)
	{
		$this->registry = $registry;
		$this->templatesDir = $templatesDir;
	}

	public function mesToHTML($messages)
	{
		$result = '';
		if (!empty($messages)) {
			foreach ($messages as $message) {
				$result .= "<p>$message</p>";
			}
		}
		return $result;
	}

	public function getTbodyHtml(Student $student)
	{
		$contents = $student->getArray();
		$tbody = '<tr>';
		foreach ($contents as $field) {
			$field = htmlspecialchars($field);
			$tbody.= "<td>{$field}</td>";
		}
		$tbody.= '</tr>';
		
		return $tbody;
	}

	public function getPaginationQuery($page = 0)
	{
		if (is_int($page)) {
			$_GET['page'] = $page;
		} else throw new \InvalidArgumentException('Parameter is not int.');
		
		ksort($_GET);
		
		$query = http_build_query($_GET);
		
		return $query;
	}
	
	public function output()
	{
		ob_start();
		$urgentMessage = '';
		$messages = $this->registry->getMessages();
		$urgentMessage = $this->mesToHTML($messages);
		
		$this->dataMapper = $this->registry->getDataMapper();
		$students = $this->dataMapper->getStudents($this->registry->getSortby(),
		                                           $this->registry->getOrder(),
		                                           $this->registry->getOffset(),
			                                       $this->registry->getLimit(),
		                                           $this->registry->getSearchText(),
		                                           $this->registry->getSearchField()
		);
		
		$entriesCount = $this->dataMapper->getEntriesCount($this->registry->getSortby(),
		                                            $this->registry->getOrder()
		);
		$pageCount = ceil($entriesCount / self::STUDENTS_IN_PAGE);
		for ($i = 1; $i <= $pageCount; $i++) {
			$queries[$i] = $this->getPaginationQuery($i);
		}
		
		
		$tbodyContent = '';
		foreach ($students as $student) {
			$tbodyContent .= $this->getTbodyHtml($student);
		}
		

		$filepath = $this->templatesDir . '/stud_list.php';
		if (file_exists($filepath)) {
			include $filepath;
		} else throw new ViewException('File doesnt exist.');

		return ob_get_clean();

	}

	public function render()
	{
		header('Content-type: text/html; charset=utf-8');
		$contents = $this->output();
		echo $contents;
	}

}