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
			$student = $this->registry->getCurrentStudent();
			$this->setFormDefaultValues($student, $defFields, false);
		} else {
			$this->setFormDefaultValues(null, $defFields, true);
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
			$out['nameVal']     = ''; $out['surnameVal']  = '';
			$out['groupVal']    = ''; $out['emailVal']    = '';
			$out['egeSumVal']   = ''; $out['birthVal']    = '';
			$out['mascVal']     = ''; $out['femVal']     = '';
			$out['localVal']    = ''; $out['nonLocalVal'] = '';
		} else {
			$out['nameVal'] = $student->getName();
			$out['surnameVal'] = $student->getSurname();
			$out['groupVal'] = $student->getGroupNum();
			$out['emailVal'] = $student->getEmail();
			$out['egeSumVal'] = $student->getEgeSum();
			$out['birthVal'] = $student->getYearOfBirth();
			if ($student->getSex() === 'М') {
				$out['mascVal'] = 'selected';
				$out['femVal'] = '';
			} elseif ($student->getSex() === 'Ж') {
				$out['mascVal'] = '';
				$out['femVal'] = 'selected';
			}
			if ($student->getLocation() === 'Местный') {
				$out['localVal'] = 'selected';
				$out['nonLocalVal'] = '';
			} elseif ($student->getLocation() === 'Иногородний') {
				$out['localVal'] = '';
				$out['nonLocalVal'] = 'selected';
			}
		}
	}
	
}