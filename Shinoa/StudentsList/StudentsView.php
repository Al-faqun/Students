<?php
namespace Shinoa\StudentsList;


use Shinoa\StudentsList\Exceptions\ViewException;

class StudentsView
{
	const STUDENTS_IN_PAGE = 20;
	private $registry = null;
	private $dataMapper = null;
	private $templatesDir = '';

	function __construct(Registry $registry, $templatesDir)
	{
		$this->registry = $registry;
		$this->templatesDir = $templatesDir;
	}

	public function mesToHTML()
	{
		
	}

	public function getTbody(Student $student)
	{
		$tbody = '<tr>' .
		            "<td>$student->getName()</td>"        . "<td>$student->getSurname()</td>"  .
		            "<td>$student->getSex()</td>"         . "<td>$student->getGroupNum()</td>" .
		            "<td>$student->getEmail()</td>"       . "<td>$student->getEgeSum()</td>"   .
		            "<td>$student->getYearOfBirth()</td>" . "<td>$student->getLocation()</td>" .
		         '</tr>';
		return $tbody;
	}

	public function output()
	{
		ob_start();
		$urgentMessage = '';
		$messages = $this->registry->getMessagesForView();
		if (!empty($messages)) {
			foreach ($messages as $message) {
				$urgentMessage .= "<p>$message</p>";
			}
		}
		
		$this->dataMapper = $this->registry->getDataMapper();
		$students = $this->dataMapper->getStudents($this->registry->getSortby(),
		                                           $this->registry->getOrder(),
		                                           $this->registry->getOffset(),
			                                       $this->registry->getLimit()
		);
		$tbodyContent = '';
		foreach ($students as $student) {
			$tbodyContent .= $this->getTbody($student);
		}
		$pageCount = ceil(count($students) / self::STUDENTS_IN_PAGE);

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