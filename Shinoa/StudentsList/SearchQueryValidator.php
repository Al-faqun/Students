<?php
namespace Shinoa\StudentsList;


class SearchQueryValidator
{
	const STUDENTS_IN_PAGE = 20;
	private $input = array();
	private $sortbyWhitelist = array('name',  'surname', 'sex',        'group_num',
	                                 'email', 'ege_sum', 'birth_year', 'location');
	private $orderWhiteList = array('ASC', 'DESC');

	public function __construct(array $input)
	{
		$this->input = $input;
	}

	public function checkSortBy()
	{
		$result = false;
		if (array_key_exists('sort_by', $this->input)
			&&
			$key = array_search($this->input['sort_by'], $this->sortbyWhitelist, true)
		) {
			$result = $this->sortbyWhitelist[$key];
		} else $result = $this->sortbyWhitelist[1];

		return $result;
	}

	public function checkOrder()
	{
		$result = false;
		if (array_key_exists('order', $this->input)
			&&
		   ($key = array_search(strtoupper($this->input['order']), $this->orderWhiteList, true))
		) {
			$result = $this->orderWhiteList[$key];
		} else $result = $this->orderWhiteList[0];

		return $result;
	}

	public function checkPage(&$offset, &$limit)
	{
		$result = false;
		if ( (array_key_exists('page', $this->input)) && ((int)$this->input['page'] > 0) ) {
			$pagenum = (int)$this->input['page'];
			$offset = ($pagenum - 1) * self::STUDENTS_IN_PAGE +1;
			$limit = $pagenum * self::STUDENTS_IN_PAGE;
		}

		return $result;
	}
}