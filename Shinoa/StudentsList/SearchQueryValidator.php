<?php
namespace Shinoa\StudentsList;

/**
 * Class SearchQueryValidator
 * @package Shinoa\StudentsList
 *
 * It checks provided input for specific keys, which combined are used in SQL.
 * It always returns valid results, either from input, or default on failure to validate.
 */
class SearchQueryValidator
{
	/**
	 *How many students displays one page.
	 */
	const STUDENTS_IN_PAGE = 20;
	
	/**
	 * @var array Contains (or not) values of various search fields, that must be checked.
	 */
	private $input = array();
	
	/**
	 * @var array Contains names of all fields, that can be used later in search.
	 */
	private $fieldsWhitelist = array('name',  'surname', 'sex',        'group_num',
	                                 'email', 'ege_sum', 'birth_year', 'location');
	
	/**
	 * @var array Contains names of key phrases, that can be used as directions of search in mysql sql.
	 */
	private $orderWhiteList = array('ASC', 'DESC');
	
	/**
	 * SearchQueryValidator constructor.
	 * @param array $input Contains (or not) values of various search fields, that must be checked.
	 */
	public function __construct(array $input)
	{
		$this->input = $input;
	}
	
	/**
	 * Checks provided earlier input array for specific field,
	 * that contains name of database field, for which text value will be searched.
	 * @return bool|mixed|string Returns string from whitelist on success,
	 * empty string if failed to find valid data, and FALSE on internal logical error.
	 */
	public function checkSearchField()
	{
		$result = false;
		$fieldname = 'search_field';
		if (array_key_exists($fieldname, $this->input)
			&&
			( ($key = array_search($this->input[$fieldname], $this->fieldsWhitelist, false)) !== false )
		) {
			$result = $this->fieldsWhitelist[$key];
		} else {
			//empty string is needed,
			// because it signals DB classes that search with search field and text is NOT needed.
			$result = '';
		}
		
		return $result;
	}
	
	/**
	 * Checks provided earlier input array for specific field,
	 * that contains text, which may be searched for in future request to DB.
	 * @return bool|mixed|string Returns string with provided text on success,
	 * empty string on false validation, and FALSE on internal logical error.
	 */
	public function checkSearchText()
	{
		$result = false;
		$fieldname = 'search_text';
		if (array_key_exists($fieldname, $this->input)) {
			$result = $this->input[$fieldname];
		} else $result = '';
		
		return $result;
	}
	
	/**
	 * Checks provided earlier input array for specific field,
	 * that denotes field, by which results from DB are sorted.
	 * @return bool|mixed Returns string with field name on success,
	 * second field on false validation, or FALSE on internal logical error.
	 */
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
	
	/**
	 * Checks provided earlier input array for specific field,
	 * that denotes order, in which results from DB are sorted.
	 * @return bool|mixed Returns string with SQL keyword on success,
	 * default value on false validation, or FALSE on internal logical error.
	 */
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
	
	/**
	 * Checks provided earlier input array for specific field
	 * @param $offset (by reference)
	 * @param $limit (by reference)
	 */
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
