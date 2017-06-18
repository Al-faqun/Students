<?php
namespace Shinoa\StudentsList;
use Shinoa\StudentsList\Exceptions\StudentException;

class SearchData
{
    //критерии поиска в базе данных
    private $text   = '';
    private $field  = '';
    private $sortby = '';
    private $order  = 'ASC';
    private $offset = 0;
    private $limit  = 0;
    
    function __construct($text, $field, $sortBy, $order, $offset, $limit)
    {
        $this->setSearchText($text);
        $this->setSearchField($field);
        $this->setSortby($sortBy);
        $this->setOrder($order);
        $this->setOffset($offset);
        $this->setLimit($limit);
    }
    
    /**
     * Memorizes text for DB queries im 'LIKE %...%' keyword.
     *
     * @param string $searchText
     */
    public function setSearchText($searchText)
    {
        $this->text = $searchText;
    }
    
    /**
     * Returns text for DB queries im 'LIKE %...%' keyword.
     *
     * @return string
     * @throws StudentException
     */
    public function getSearchText()
    {
        if ( isset($this->text) ) {
            return $this->text;
        } else throw new StudentException('Trying to retrieve empty parameter');
    }
    
    /**
     * Memorizes textname of field for DB queries.
     *
     * @param string $searchField
     */
    public function setSearchField($searchField)
    {
        $this->field = $searchField;
    }
    
    /**
     * Returns textname of field for DB queries.
     *
     * @return string
     * @throws StudentException
     */
    public function getSearchField()
    {
        if ( isset($this->field) ) {
            return $this->field;
        } else throw new StudentException('Trying to retrieve empty parameter');
        
    }
    
    /**
     * Memorizes textname of field, used in DB queries for sorting results.
     *
     * @param string $sortby
     */
    public function setSortby($sortby)
    {
        $this->sortby = $sortby;
    }
    
    /**
     * Returns textname of field, used in DB queries for sorting results.
     * @return string
     * @throws StudentException
     */
    public function getSortby()
    {
        if ( isset($this->sortby) ) {
            return $this->sortby;
        } else throw new StudentException('Trying to retrieve empty parameter');
    }
    
    /**
     * Memorizes string, representing sql keyword 'ASC' or 'DESC'.
     *
     * @param string $order
     */
    public function setOrder($order)
    {
        $this->order = $order;
    }
    
    /**
     * Returns string, representing sql keyword 'ASC' or 'DESC'.
     * @return string
     */
    public function getOrder()
    {
        if ( isset($this->order) ) {
            return $this->order;
        } else throw new StudentException('Trying to retrieve empty parameter');
    }
    
    /**
     * Memorizes integer, used in sql queries after 'LIMIT' keyword.
     *
     * @param int $offset
     */
    public function setOffset($offset)
    {
        $this->offset = $offset;
    }
    
    /**
     * Returns integer, used in sql queries after 'OFFSET' keyword.
     *
     * @return int
     * @throws StudentException
     */
    public function getOffset()
    {
        if ( isset($this->offset) ) {
            return $this->offset;
        } else throw new StudentException('Trying to retrieve empty parameter');
    }
    
    /**
     * Memorizes integer, used in sql queries after 'LIMIT' keyword.
     *
     * @param int $limit
     */
    public function setLimit($limit)
    {
        $this->limit = $limit;
    }
    
    /**
     * Returns integer, used in sql queries after 'LIMIT' keyword.
     *
     * @return int
     * @throws StudentException
     */
    public function getLimit()
    {
        if ( isset($this->limit) ) {
            return $this->limit;
        } else throw new StudentException('Trying to retrieve empty parameter');
    }
}