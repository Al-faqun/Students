<?php
namespace Shinoa\StudentsList;

use PhpParser\Node\Expr\Variable;
use Shinoa\StudentsList\Exceptions\StudentException;

/**
 * Class StudentValidator
 * @package Shinoa\StudentsList
 *
 * This class is used to check whether data сomports to Student data restrictions.
 */
class StudentValidator
{
	/**
	 * @var array List of all legit values of 'sex' field of input data.
	 */
	private static  $sexWhitelist = ['masculine', 'feminine'];
	
	/**
	 * @var array List of all legit values of 'location' field of input data.
	 */
	private static $locationWhiteList = ['local', 'non_local'];
	
	/**
	 * @var StudentMapper
	 */
	private $mapper;
	
	/**
	 * @var array
	 */
	private $input;
	
	/**
	 * StudentValidator constructor.
	 * @param StudentMapper $mapper
	 * @param array $input Array of GET, POST, SESSION or other.
	 * It must contain fields of student and also 'form_sent' hidden field.
	 */
	public function __construct(StudentMapper $mapper, array $input = [])
	{
		$this->mapper = $mapper;
		$this->input = $input;
	}
	
	/**
	 * @return bool TRUE if student data was sent FALSE is not.
	 */
	public function dataSent()
	{
		$input = $this->input;
		if ( isset($input['form_sent']) ) {
			$dataSent = true;
		} else {
			$dataSent = false;
		}
		return $dataSent;
	}
	
	/**
	 * @param array $input Array of GET, POST, SESSION or other.
	 * It must contain fields of student and also 'form_sent'.
	 * @param null|array $errors (by reference). This variable is emptied and then filled with messages
	 * if some student field is not right. Use it to check for succesful validation and also to display messages.
	 * @param bool $dataSent  Indicated whether the  data of student was indeed sent.
	 * @param bool $update TRUE if data would be updated, false if inserted.
	 * @return bool|Student Student if everything allright, otherwise FALSE.
	 */
	public function checkInput(&$errors, $dataSent, $update = false)
	{
		$errors = array();
		$input = $this->input;
		if ($dataSent) {
			$name      = ( isset($input['name']) )       ? $this->checkName($input['name'])            : false;
			$surname   = ( isset($input['surname']) )    ? $this->checkSurname($input['surname'])      : false;
			$sex       = ( isset($input['sex']) )        ? $this->checkSex($input['sex'])              : false;
			$groupNum  = ( isset($input['group_num']) )  ? $this->checkGroupNum($input['group_num'])   : false;
			$email     = ( isset($input['email']) )      ? $this->checkEmail($input['email'], $update)          : false;
			$egeSum    = ( isset($input['ege_sum']) )    ? $this->checkEgeSum($input['ege_sum'])       : false;
			$birthYear = ( isset($input['birth_year']) ) ? $this->checkBirthYear($input['birth_year']) : false;
			$location  = ( isset($input['location']) )   ? $this->checkLocation($input['location'])    : false;
			
			self::mesIfFalse($name,      $errors, 'Имя');
			self::mesIfFalse($surname,   $errors, 'Фамилия');
			self::mesIfFalse($sex,       $errors, 'Пол');
			self::mesIfFalse($groupNum,  $errors, 'Номер группы');
			self::mesIfFalse($email,     $errors, 'Почта');
			self::mesIfFalse($egeSum,    $errors, 'Сумма баллов ЕГЭ');
			self::mesIfFalse($birthYear, $errors, 'Год рождения');
			self::mesIfFalse($location,  $errors, 'Происхождение');
			
			if (empty($errors)) {
				$result = new Student(trim($name),      trim($surname), trim($sex),
				                      trim($groupNum),  trim($email),   trim($egeSum),
					                  trim($birthYear), trim($location));
			} else {
				$result = false;
			}
		} else $result = false;
		
		return $result;
	}
	
	/**
	 * Checks Student object.
	 * @param Student $in Student object to test for validity.
	 * @param array $errors (by reference). This array is emptied and then filled with messages
	 * if some student field is not right. Use it to check for succesful validation and also to display messages.
	 * @param bool $updated TRUE if data would be updated, false if inserted.
	 * @return bool TRUE if Student complies to rules of validator, else FALSE.
	 */
	public function checkStudent(Student $in, &$errors, $updated = false)
	{
		$name        = $this->checkName( $in->getName() );
		$surname     = $this->checkSurname( $in->getSurname() );
		$sex         = $this->checkSex( $in->getSex() );
		$groupNum    = $this->checkGroupNum( $in->getGroupNum() );
		$email       = $this->checkEmail($in->getEmail(), $updated);
		$egeSum      = $this->checkEgeSum( $in->getEgeSum() );
		$yearOfBirth = $this->checkBirthYear( $in->getYearOfBirth() );
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
	
	/**
	 * If variable is false, this method adds to array warning string message.
	 * @param $var Variable to test against FALSE
	 * @param $outputArray (by reference) Array of strings.
	 * @param string $fieldName Text name of field, which will be used in message.
	 */
	private static function mesIfFalse($var, &$outputArray, $fieldName)
	{
		if ($var === false) {
			$outputArray[] = "Поле \"$fieldName\" заполненно некорректно. Исправьте!";
		}
	}
	
	/**
	 * Checks whether text variable starts with unicode Letter.
	 * @param $string var Variable to test.
	 * @return bool TRUE if var starts with letter (case insensitive), else FALSE.
	 */
	private static function startsWithLetter($var)
	{
		if ( preg_match('/^\p{L}/iu', $var) ) {
			return true;
		} else return false;
	}
	
	/**
	 * Checks string to consist of letters, numbers and '_' sign.
	 * @param string $string String to check
	 * @param int $minlen Minimal permitted length of string to pass check.
	 * @param int $maxlen Maximal permitted length of string to pass check.
	 * @param bool $startsWithLetter Optional parameter is used,
	 * when first meaningful symbol of string (except any white character) must be letter.
	 * @return bool|string Returns string if it passes test, else FALSE
	 * (be careful, any whitespace character in the begginning and the end are deleted).
	 */
	public static function checkString($string, $minlen, $maxlen, $startsWithLetter = false)
	{
		if (is_string($string)) {
			$string = trim($string);
			if (!is_int($minlen) || !is_int($maxlen)) {
				throw new \UnexpectedValueException('Length of string must be integer');
			}
			if ( (mb_strlen($string) >= $minlen
			       &&
			      mb_strlen($string) <= $maxlen)
				   &&
				 (preg_match('/^\w+$/iu', $string) > 0)
			) {
				$result = $string;
			} else $result = false;
			
			if ($startsWithLetter === true && !self::startsWithLetter($string)) {
				$result = false;
			}
		} else $result = false;
		
		return $result;
	}
	
	/**
	 * Checks string with rules for 'Name' field.
	 * @param string $name
	 * @return bool|string String if checks right or FALSE on fail.
	 */
	private function checkName($name)
	{
		return self::checkString($name, 1, 100, true);
	}
	
	/**
	 * Checks string with rules for 'Surname' field.
	 * @param string $surname
	 * @return bool|string String if checks right or FALSE on fail.
	 */
	private function checkSurname($surname)
	{
		return self::checkString($surname, 1, 100, true);
	}
	
	/**
	 * Checks string with rules for 'Sex' field.
	 * @param string $sex
	 * @return bool|string String if checks right or FALSE on fail.
	 */
	private function checkSex($sex)
	{
		$sex = trim($sex);
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
	
	/**
	 * Checks string with rules for 'GroupNum' field.
	 * @param string $groupNum
	 * @return bool|string String if checks right or FALSE on fail.
	 */
	private function checkGroupNum($groupNum)
	{
		return self::checkString($groupNum, 1, 5);
	}
	
	/**
	 * Checks string with rules for 'Email' field.
	 * @param string $email
	 * @param bool $update TRUE if data would be updated, FALSE if inserted.
	 * @return bool|string String if checks right or FALSE on fail.
	 */
	private function checkEmail($email, $update = false)
	{
		$email = trim($email);
		if ( filter_var($email, FILTER_VALIDATE_EMAIL)
			  &&
			//если значение обновляется, а не вставляется
			//- проверка на уникальность противопоказана
			( !$this->mapper->existsValue('email', $email)
				|| $update !== false)
		) {
			$result = $email;
		} else $result = false;
		return $result;
	}
	
	/**
	 * Checks string with rules for 'Egesum' field.
	 * @param string $egeSum
	 * @return bool|string String if checks right or FALSE on fail.
	 */
	private function checkEgeSum($egeSum)
	{
		$egeSum = trim($egeSum);
		$options = array(
			'options' => array('min_range' => 1, 'max_range' => 500)
		);
		if ( filter_var($egeSum, FILTER_VALIDATE_INT, $options) ) {
			$result = $egeSum;
		} else $result = false;
		return $result;
	}
	
	/**
	 * Checks string with rules for 'BirthYear' field.
	 * @param string $birthYear
	 * @return bool|string String if checks right or FALSE on fail.
	 */
	private function checkBirthYear($birthYear)
	{
		$birthYear = trim($birthYear);
		$options = array(
			'options' => array('min_range' => 1900, 'max_range' => 2017)
		);
		if ( filter_var($birthYear, FILTER_VALIDATE_INT, $options) ) {
			$result = $birthYear;
		} else $result = false;
		return $result;
	}
	
	/**
	 * Checks string with rules for 'Location' field.
	 * @param string $location
	 * @return bool|string String if checks right or FALSE on fail.
	 */
	private function checkLocation($location)
	{
		$location = trim($location);
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

