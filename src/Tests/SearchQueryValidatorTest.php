<?php
namespace Shinoa\StudentsList\Tests;
use PHPUnit\Framework\TestCase;
use Shinoa\StudentsList\Input\SearchQueryValidator;


class SearchQueryValidatorTest extends TestCase
{
	private $sortbyWhitelist = array('name',  'surname', 'sex',        'group_num',
	                                 'email', 'ege_sum', 'birth_year', 'location');
	private $orderWhiteList = array('ASC', 'DESC');

	public function setUp()
	{
	}

	public function tearDown()
	{
	}

	public function testCheckSortByWhiteList()
	{
		$array = array('кусь' => 'кусьvalue', 'цап' => 'цапvalue', 'sort_by' => 'ege_sum');
		$validator = new SearchQueryValidator($array);
		$sort_by = $validator->checkSortBy();

		$this->assertContains($sort_by, $this->sortbyWhitelist, 'Значение sort_by не из разрешённого списка');
	}
	
	public function testCheckSortByExact()
	{
		$expectedValue = 'ege_sum';
		$array = array('кусь' => 'кусьvalue', 'цап' => 'цапvalue', 'sort_by' => $expectedValue);
		$validator = new SearchQueryValidator($array);
		$sort_by = $validator->checkSortBy();
		
		$this->assertEquals($expectedValue, $sort_by);
	}

	public function testCheckSortByFailNum()
	{
		$array = array('кусь' => 'кусьvalue', 'цап' => 'цапvalue', 'sort_by' => 5);
		$validator = new SearchQueryValidator($array);
		$sort_by = $validator->checkSortBy();

		$this->assertContains($sort_by, $this->sortbyWhitelist, 'Значение sort_by не из разрешённого списка');
	}

	public function testCheckOrderWhiteList()
	{
		$array = array('кусь' => 'кусьvalue', 'цап' => 'цапvalue', 'order' => 'asc');
		$validator = new SearchQueryValidator($array);
		$order = $validator->checkOrder();

		$this->assertContains($order, $this->orderWhiteList, 'Значение order не из разрешённого списка');
	}
	
	public function testCheckOrderExact()
	{
		$expectedValue = 'asc';
		$array = array('кусь' => 'кусьvalue', 'цап' => 'цапvalue', 'order' => $expectedValue);
		$validator = new SearchQueryValidator($array);
		$order = $validator->checkOrder();
		
		$this->assertEquals($expectedValue, $order, '',  0, 10, false, true);
	}
	
	public function testCheckOrderFailNum()
	{
		$array = array('кусь' => 'кусьvalue', 'цап' => 'цапvalue', 'order' => 5);
		$validator = new SearchQueryValidator($array);
		$order = $validator->checkOrder();

		$this->assertContains($order, $this->orderWhiteList, 'Значение order не из разрешённого списка');
	}

	public function testCheckPage()
	{
		$array = array('кусь' => 'кусьvalue', 'цап' => 'цапvalue', 'page' => '3');
		$validator = new SearchQueryValidator($array);
		$validator->checkPage($offset, $limit);

		$this->assertInternalType('int', $offset);
		$this->assertInternalType('int', $limit);
	}

	public function testCheckPageFailZero()
	{
		$array = array('кусь' => 'кусьvalue', 'цап' => 'цапvalue', 'page' => '0');
		$validator = new SearchQueryValidator($array);
		$validator->checkPage($offset, $limit);

		$this->assertInternalType('int', $offset);
		$this->assertInternalType('int', $limit);
	}

	public function testCheckPageFailDouble()
	{
		$array = array('кусь' => 'кусьvalue', 'цап' => 'цапvalue', 'page' => '6.6');
		$validator = new SearchQueryValidator($array);
		$validator->checkPage($offset, $limit);

		$this->assertInternalType('int', $offset);
		$this->assertInternalType('int', $limit);
	}

public function testCheckPageFailString()
	{
		$array = array('кусь' => 'кусьvalue', 'цап' => 'цапvalue', 'page' => 'волшебство');
		$validator = new SearchQueryValidator($array);
		$validator->checkPage($offset, $limit);

		$this->assertInternalType('int', $offset);
		$this->assertInternalType('int', $limit);
	}


}
