<?php
namespace Shinoa\StudentsList;

use Shinoa\StudentsList\Exceptions\StudentException;

/**
 * Class Student
 * @package Shinoa\StudentsList
 * Contains data of student (from database, input or other source).
 *
 * Allowes it's transformations.
 *
 * Be careful! This class contains no validation (except casting egesum and birthyear to int).
 * Use dedicated validators if you want this data to apply for constraints.
 */
class Student
{
    private $name = '';
    private $surname = '';
    private $sex = '';
    private $group_num = '';
    private $email = '';
    private $ege_sum = 0;
    private $birth_year = 0;
    private $location = '';
	public static $databaseFields = array ('name',  'surname', 'sex', 'group_num',
	                                 'email', 'ege_sum', 'birth_year', 'location');

	public function __construct($name, $surname, $sex,
                                $groupNum, $email, $egeSum,
                                $yearOfBirth, $location)
    {
    	$this->name = $name;
    	$this->surname = $surname;
    	$this->sex = $sex;
    	$this->group_num = $groupNum;
    	$this->email = $email;
    	$this->ege_sum = (int)$egeSum;
    	$this->birth_year = (int)$yearOfBirth;
    	$this->location = $location;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getSurname()
    {
        return $this->surname;
    }

    /**
     * @return string
     */
    public function getSex()
    {
        return $this->sex;
    }

    /**
     * @return int
     */
    public function getGroupNum()
    {
        return $this->group_num;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @return int
     */
    public function getEgeSum()
    {
        return $this->ege_sum;
    }

    /**
     * @return int
     */
    public function getYearOfBirth()
    {
        return $this->birth_year;
    }

    /**
     * @return string
     */
    public function getLocation()
    {
        return $this->location;
    }
	
	/**
	 * Returns associative map with keys like object's public fields and values as its values.
	 * @return array
	 */
	public function getArray()
	{
		$result = array(
			'name'       => $this->name,        'surname' => $this->surname,
			'sex'        => $this->sex,       'group_num' => $this->group_num,
			'email'      => $this->email,       'ege_sum' => $this->ege_sum,
			'birth_year' => $this->birth_year, 'location' => $this->location );
		return $result;
    }
	
	/**
	 * Makes Student object from array's fields. If correct field is not specified,
	 * empty string '' is set to object's field instead.
	 * @param array $array Contains fields with names of class's variables.
	 * @return Student
	 */
	public static function makeStudentFromArray(array $array)
	{
		$name      = $array['name']      ?? '';
		$surname   = $array['surname']   ?? '';
		$sex       = $array['sex']       ?? '';
		$groupNum  = $array['group_num'] ?? '';
		$email     = $array['email']     ?? '';
		$egeSum    = ( isset($array['ege_sum']) )     ? (int)$array['ege_sum']     : '';
		$birthYear = ( isset($array['birth_year']) )  ? (int)$array['birth_year']  : '';
		$location  = $array['location']  ?? '';

		$student = new Student($name,  $surname,    $sex,
		                       $groupNum,  $email, $egeSum,
		                       $birthYear, $location);
		return $student;
	}

}
