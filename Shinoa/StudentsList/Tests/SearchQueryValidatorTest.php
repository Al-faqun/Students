<?php
namespace Shinoa\StudentsList\Tests;
use Shinoa\StudentsList\SearchQueryValidator;


class SearchQueryValidatorTest extends \PHPUnit_Framework_TestCase
{
	public function setUp()
	{
		$array = array('кусь' => 'кусьvalue', 'цап' => 'цапvalue', 'sort_by' = '')
		$validator = new SearchQueryValidator();
	}

	public function tearDown()
	{

	}

	public function checkSortByTest()
	{

	}
}
