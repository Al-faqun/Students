<?php
namespace Shinoa\StudentsList;


class SearchQueryValidator
{
	const STUDENTS_IN_PAGE = 20;
	private $input = array();
	private $fieldsWhitelist = array('name',  'surname', 'sex',        'group_num',
	                                 'email', 'ege_sum', 'birth_year', 'location');
	private $orderWhiteList = array('ASC', 'DESC');

	public function __construct(array $input)
	{
		$this->input = $input;
	}
	
	public function checkSearchField()
	{
		$result = false;
		$fieldname = 'search_field';
		if (array_key_exists($fieldname, $this->input)
			&&
			( ($key = array_search($this->input[$fieldname], $this->fieldsWhitelist, false)) !== false )
		) {
			$result = $this->fieldsWhitelist[$key];
		} else $result = '';
		
		return $result;
	}
	
	public function checkSearchText()
	{
		$result = false;
		$fieldname = 'search_text';
		if (array_key_exists($fieldname, $this->input)) {
			$result = $this->input[$fieldname];
		} else $result = '';
		
		return $result;
	}
	public function checkSortBy()
	{
		$result = false;
		$fieldname = 'sort_by';
		if (array_key_exists($fieldname, $this->input)
			&&
			(($key = array_search($this->input[$fieldname], $this->fieldsWhitelist, false)) !== false)
		) {
			$result = $this->fieldsWhitelist[$key];
		} else $result = $this->fieldsWhitelist[1];
		
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
		if ( (array_key_exists('page', $this->input)) && ((int)$this->input['page'] > 0) ) {
			$pagenum = (int)$this->input['page'];
			$offset = ($pagenum - 1) * self::STUDENTS_IN_PAGE;
			$limit = $pagenum * self::STUDENTS_IN_PAGE;
		} else {
			$offset = 0;
			$limit = self::STUDENTS_IN_PAGE;
		}
	}
}