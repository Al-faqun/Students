<?php
namespace Shinoa\StudentsList;

class Student
{
    private $name = '';
    private $surname = '';
    private $sex = '';
    private $group_num = 0;
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
	
	public function getArray()
	{
		$result = array(
			'name'       => $this->name,        'surname' => $this->surname,
			'sex'        => $this->sex,       'group_num' => $this->group_num,
			'email'      => $this->email,       'ege_sum' => $this->ege_sum,
			'birth_year' => $this->birth_year, 'location' => $this->location );
		return $result;
    }

}
