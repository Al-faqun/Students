<?php
namespace Shinoa\StudentsList;


use Shinoa\StudentsList\Exceptions\ViewException;

class RegEditView
{
	private $registry;
	private $dataMapper;
	private $templatesDir = '';
	
	function __construct(Registry $registry, $templatesDir)
	{
		$this->registry = $registry;
		$this->templatesDir = $templatesDir;
	}
	
	public static function esc($value)
	{
		$result = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
		return $result;
	}
	
	public static function escAll(&$array)
	{
		foreach ($array as $key => $value) {
			$value = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
			$array[$key] = $value;
		}
	}
	
	public function mesToHTML($messages)
	{
		$result = '';
		if (!empty($messages)) {
			foreach ($messages as $message) {
				$message = self::esc($message);
				$result .= "<p>$message</p>";
			}
		}
		return $result;
	}
	
	public function output()
	{
		ob_start();
		
		$this->dataMapper = $this->registry->getDataMapper();
		
		$caption = ($this->registry->isLogged()) ? 'Обновить данные' : 'Регистрация';
		$submitButName = 'Отправить';
		
		$defFields = [];
		$error = $this->mesToHTML( $this->registry->getErrors() );
		$mesagge = $this->mesToHTML( $this->registry->getMessages() );
		
		if ( $this->registry->isLogged() ) {
			if (!empty($error)) {
				$student = $this->registry->getStudentData();
			} else {
				$student = $this->registry->getCurrentStudentFromDB();
			}
			$this->setFormDefaultValues($student, $defFields, false);
		} elseif ($this->registry->isLogged() === false) {
			if (!empty($error)) {
				$student = $this->registry->getStudentData();
				$this->setFormDefaultValues($student, $defFields, false);
			} else {
				$this->setFormDefaultValues(null, $defFields, true);
			}
			
		}
		self::escAll($defFields);
		
		$filepath = $this->templatesDir . '/tpl_reg-edit.php';
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
	
	private function setFormDefaultValues($student, &$out, $makeEmpty = true)
	{
		$out = array();
		if ( $makeEmpty || ($student === false) ) {
			$out['nameVal']   = '';   $out['surnameVal']  = '';
			$out['groupVal']  = '';   $out['emailVal']    = '';
			$out['egeSumVal'] = '';   $out['birthVal']    = '';
			$out['mascVal']   = '';   $out['femVal']     = '';
			$out['localVal']  = '';   $out['nonLocalVal'] = '';
		} else {
			$out['nameVal']    = $student->getName();
			$out['surnameVal'] = $student->getSurname();
			$out['groupVal']   = $student->getGroupNum();
			$out['emailVal']   = $student->getEmail();
			$out['egeSumVal']  = $student->getEgeSum();
			$out['birthVal']   = $student->getYearOfBirth();
			
			switch ( $student->getSex() ) {
				case 'М':
					$out['mascVal'] = 'selected';
					$out['femVal'] = '';
					break;
				case 'Ж':
					$out['mascVal'] = '';
					$out['femVal'] = 'selected';
					break;
				default:
					$out['mascVal'] = '';
					$out['femVal'] = '';
			}
			switch ( $student->getLocation() ) {
				case 'Местный':
					$out['localVal'] = 'selected';
					$out['nonLocalVal'] = '';
					break;
				case 'Иногородний':
					$out['localVal'] = '';
					$out['nonLocalVal'] = 'selected';
					break;
				default:
					$out['localVal'] = '';
					$out['nonLocalVal'] = 'selected';
					break;
			}
		}
	}
	
}