<?php
namespace Shinoa\StudentsList;


use Shinoa\StudentsList\Exceptions\StudentException;

class StudentValidator
{
	private static  $sexWhitelist = ['masculine', 'feminine'];
	private static $locationWhiteList = ['local', 'non_local'];
	
	public function __construct()
	{
	}
	
	
	
	public function checkInput($input, array &$errors, &$dataSent)
	{
		$errors = array();
		if ( isset($input['form_sent']) ) {
			$dataSent = true;
		} else {
			$dataSent = false;
		}
		
		if ($dataSent) {
			$name = (isset($input['name'])) ? $this->checkName($input['name']) : false;
			$surname = (isset($input['surname'])) ? $this->checkSurname($input['surname']) : false;
			$sex = (isset($input['sex'])) ? $this->checkSex($input['sex']) : false;
			$groupNum = (isset($input['group_num'])) ? $this->checkGroupNum($input['group_num']) : false;
			$email = (isset($input['email'])) ? $this->checkEmail($input['email']) : false;
			$egeSum = (isset($input['ege_sum'])) ? $this->checkEgeSum($input['ege_sum']) : false;
			$birthYear = (isset($input['birth_year'])) ? $this->checkBirthYear($input['birth_year']) : false;
			$location = (isset($input['location'])) ? $this->checkLocation($input['location']) : false;
			
			self::mesIfFalse($name, $errors, 'Имя');
			self::mesIfFalse($surname, $errors, 'Фамилия');
			self::mesIfFalse($sex, $errors, 'Пол');
			self::mesIfFalse($groupNum, $errors, 'Номер группы');
			self::mesIfFalse($email, $errors, 'Почта');
			self::mesIfFalse($egeSum, $errors, 'Сумма баллов ЕГЭ');
			self::mesIfFalse($birthYear, $errors, 'Год рождения');
			self::mesIfFalse($location, $errors, 'Происхождение');
			
			if (empty($errors)) {
				$result = new Student($name, $surname, $sex,
					$groupNum, $email, $egeSum,
					$birthYear, $location);
			} else {
				$result = false;
			}
		} else $result = false;
		
		return $result;
	}
	
	public function checkStudent(Student $in, array &$errors)
	{
		$name        = $this->checkName( $in->getName() );
		$surname     = $this->checkSurname( $in->getSurname() );
		$sex         = $this->checkSex( $in->getSex() );
		$groupNum    = $this->checkGroupNum( $in->getGroupNum() );
		$email       = $this->checkEmail( $in->getEmail() );
		$egeSum      = $this->checkEgeSum( $in->getEgeSum() );
		$yearOfBirth = $this->checkYearOfBirth( $in->getYearOfBirth() );
		$location    = $this->checkLocation ($in->getLocation() );
		
		self::mesIfFalse($name,        $errors, 'Имя');
		self::mesIfFalse($surname,     $errors, 'Фамилия');
		self::mesIfFalse($sex,         $errors, 'Пол');
		self::mesIfFalse($groupNum,    $errors, 'Номер группы');
		self::mesIfFalse($email,       $errors, 'Почта');
		self::mesIfFalse($egeSum,      $errors, 'Сумма баллов ЕГЭ');
		self::mesIfFalse($yearOfBirth, $errors, 'Год рождения');
		self::mesIfFalse($location,    $errors, 'Происхождение');
		if ( empty($errors) ) {
			$result = true;
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
	
	private function checkName($name)
	{
		return self::checkString($name, 1, 100, true);
	}
	
	private function checkSurname($surname)
	{
		return self::checkString($surname, 1, 100, true);
	}
	
	private function checkSex($sex)
	{
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
	
	private function checkGroupNum($groupNum)
	{
		return self::checkString($groupNum, 1, 5);
	}
	
	private function checkEmail($email)
	{
		if ( filter_var($email, FILTER_VALIDATE_EMAIL) ) {
			$result = $email;
		} else $result = false;
		return $result;
	}
	
	private function checkEgeSum($egeSum)
	{
		$options = ['min_range' => 1, 'max_range' => 500];
		if ( filter_var($egeSum, FILTER_VALIDATE_INT, $options) ) {
			$result = $egeSum;
		} else $result = false;
		return $result;
	}
	
	private function checkBirthYear($birthYear)
	{
		$options = ['min_range' => 1900, 'max_range' => 2017];
		if ( filter_var($birthYear, FILTER_VALIDATE_INT, $options) ) {
			$result = $birthYear;
		} else $result = false;
		return $result;
	}
	
	private function checkLocation($location)
	{
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

