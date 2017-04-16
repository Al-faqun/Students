<?php
namespace Shinoa\StudentsList;


class StudentValidator
{
	private static  $sexWhitelist = ['masculine', 'feminine'];
	private static $locationWhiteList = ['local', 'non_local'];
	
	public function __construct()
	{
	}
	
	public function check(Student $in, &$errors)
	{
		$errors = [];
		$name = $this->checkName($in);
		$surname = $this->checkSurname($in);
		$sex = $this->checkSex($in);
		$groupNum = $this->checkGroupNum($in);
		$email = $this->checkEmail($in);
		$egeSum = $this->checkEgeSum($in);
		$yearOfBirth = $this->checkYearOfBirth($in);
		$location = $this->checkLocation($in);
		
		self::mesIfFalse($name,        $errors,        'Имя');
		self::mesIfFalse($surname,     $errors,        'Фамилия');
		self::mesIfFalse($sex,         $errors,        'Пол');
		self::mesIfFalse($groupNum,    $errors,        'Номер группы');
		self::mesIfFalse($email,       $errors,        'Почта');
		self::mesIfFalse($egeSum,      $errors,        'Сумма баллов ЕГЭ');
		self::mesIfFalse($yearOfBirth, $errors,        'Год рождения');
		self::mesIfFalse($location,    $errors,        'Происхождение');
		if ( empty($errors) ) {
			$result = new Student($name,  $surname, $sex, $groupNum,
				               $email, $egeSum,  $yearOfBirth, $location);
		} else {
			$result = false;
		}
		
		return $result;
	}
	
	private static function mesIfFalse($var, &$outputArray, $fieldName)
	{
		if ($var === false) {
			$outputArray[] = "Поле \"$fieldName\" заполненно некорректно. Исправьте!";
		}
	}
	
	public static function startsWithLetter($var)
	{
		if ( preg_match('/^\p{L}/iu', $var) ) {
			return true;
		} else return false;
	}
	
	private static function checkString($string, $minlen, $maxlen, $startsWithLetter = false)
	{
		if ( !is_int($minlen) || !is_int($maxlen) ) {
			throw new \UnexpectedValueException('Length of string must be integer');
		}
		if ( is_string($string)
			&&
			(mb_strlen($string) >= $minlen && mb_strlen($string) <= $maxlen)
		) {
			$result = $string;
		} else $result = false;
		
		if ( $startsWithLetter !== false && !self::startsWithLetter($string) ) {
			$result = false;
		}
		return $result;
	}
	
	private function checkName(Student $in)
	{
		$name = $in->getName();
		return self::checkString($name, 1, 100, true);
	}
	
	private function checkSurname(Student $in)
	{
		$surname = $in->getSurname();
		return self::checkString($surname, 1, 100, true);
	}
	
	private function checkSex(Student $in)
	{
		$sex = $in->getSex();
		if ( ($key = array_search(mb_strtolower($sex), self::$sexWhitelist, false)) !== false
		) {
			switch ($key) {
				case 0:
					$result = 'М';
					break;
				case 1:
					$result = 'Ж';
					break;
				default:
					throw new \LogicException('Incorrect switch branch');
			}
		} else $result = false;
		return $result;
	}
	
	private function checkGroupNum(Student $in)
	{
		$groupNum = $in->getGroupNum();
		return self::checkString($groupNum, 1, 5);
	}
	
	private function checkEmail(Student $in)
	{
		$email = $in->getEmail();
		if ( filter_var($email, FILTER_VALIDATE_EMAIL) ) {
			$result = $email;
		} else $result = false;
		return $result;
	}
	
	private function checkEgeSum(Student $in)
	{
		$egeSum = $in->getEgeSum();
		$options = ['min_range' => 1, 'max_range' => 500];
		if ( filter_var($egeSum, FILTER_VALIDATE_INT, $options) ) {
			$result = $egeSum;
		} else $result = false;
		return $result;
	}
	
	private function checkYearOfBirth(Student $in)
	{
		$yearOfBirth = $in->getYearOfBirth();
		$options = ['min_range' => 1900, 'max_range' => 2017];
		if ( filter_var($yearOfBirth, FILTER_VALIDATE_INT, $options) ) {
			$result = $yearOfBirth;
		} else $result = false;
		return $result;
	}
	
	private function checkLocation(Student $in)
	{
		$location = $in->getLocation();
		if ( ($key = array_search(mb_strtolower($location), self::$locationWhiteList, false)) !== false
		) {
			switch ($key) {
				case 0:
					$result = 'Местный';
					break;
				case 1:
					$result = 'Иногородний';
					break;
				default:
					throw new \LogicException('Incorrect switch branch');
			}
		} else $result = false;
		return $result;
	}
	
}

