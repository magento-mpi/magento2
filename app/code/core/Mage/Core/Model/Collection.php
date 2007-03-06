<?php

/**
 * Base items collection class 
 *
 * @package    Mage
 * @module     Core
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 * @author     Andrey Korolyov
 * @author     Moshe Gurvich <moshe@varien.com>
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Mage_Core_Model_Collection implements Iterator
{
    // ITEMS
    protected $_items = array();
    protected $_itemObjectClass='Varien_DataObject';
    
    // DB
    protected $_dbModel;
    
    // SQL
    protected $_orders      = array();
    protected $_filters     = array();
    protected $_isFiltersRendered = false;
    protected $_sqlSelect;
    
    // PAGER
    protected $_curPage     = 1;
    protected $_pageSize    = 10;
    protected $_totalRecords= null;
    
    // ITERATOR
    protected $_counter = 0;
    
	public function __construct(Mage_Core_Model_Db $db) 
	{
		$this->_dbModel = $db;

		if (!$this->_dbModel->getReadConnection() instanceof Zend_Db_Adapter_Abstract) {
			Mage::exception('dbModel read resource does not implement Zend_Db_Adapter_Abstract', 0, 'Mage_Core');
		}
		$this->_sqlSelect = $this->_dbModel->getReadConnection()->select();
	}
	
	/**
	 * Add collection filter
	 *
	 * @param string $field
	 * @param string $value
	 * @param string $type and|or|string
	 */
	public function addFilter($field, $value, $type = 'and')
	{
	    $filter = array();
	    $filter['field']   = $field;
	    $filter['value']   = $value;
	    $filter['type']    = strtolower($type);
	    
	    $this->_filters[] = $filter;
	    $this->_isFiltersRendered = false;
		return $this;
	}
	
	/**
	 * Get current collection page
	 *
	 * @param  int $displacement
	 * @return int
	 */
	public function getCurPage($displacement = 0)
	{
        if ($this->_curPage + $displacement < 1) {
            return 1;
        }
        elseif ($this->_curPage + $displacement > $this->getLastPageNumber()) {
            return $this->getLastPageNumber();
        } else {
            return $this->_curPage + $displacement;
        }
	}
    
	/**
	 * Get last page number
	 *
	 * @return int
	 */
    public function getLastPageNumber()
    {
        $collectionSize = $this->getSize();
        if (0 === $collectionSize) {
            return 1;
        } else {
            return ceil($collectionSize/$this->_pageSize);
        }
    }
    
    /**
     * Get collection size
     *
     * @return int
     */
    public function getSize()
    {
        if (is_null($this->_totalRecords)) {
            $sql = $this->getSelectCountSql();
            $this->_totalRecords = $this->_dbModel->getReadConnection()->fetchOne($sql);
        }
        return $this->_totalRecords;
    }
    
    /**
     * Get sql for get record count
     *
     * @return  string
     */
    public function getSelectCountSql()
    {
        $this->_renderFilters();
    	
        $countSelect = clone $this->_sqlSelect;
    	$countSelect->reset(Zend_Db_Select::ORDER);
    	$countSelect->reset(Zend_Db_Select::LIMIT_COUNT);
    	$countSelect->reset(Zend_Db_Select::LIMIT_OFFSET);
    	
    	// TODO: $ql->from('table',new Zend_Db_Expr('COUNT(*)'));
    	$sql = $countSelect->__toString();
    	$sql = preg_replace('/^(.*)from/is', 'select count(*) from', $sql);
    	return $sql;
    }
    
    /**
     * Get sql select string or object
     *
     * @param   bool $stringMode
     * @return  string || Zend_Db_Select
     */
    function getSelectSql($stringMode = false)
    {
    	if ($stringMode) {
    		return $this->_sqlSelect->__toString();
    	}
    	return $this->_sqlSelect;
    }
    
    /**
     * Set current page
     *
     * @param   int $page 
     * @return  Mage_Core_Model_Collection
     */
    public function setCurPage($page)
    {
        $this->_curPage = $page;
        return $this;
    }
    
    /**
     * Set collection page size
     *
     * @param   int $size
     * @return  Mage_Core_Model_Collection
     */
    public function setPageSize($size)
    {
        $this->_pageSize = $size;
        return $this;
    }
    
    /**
     * Set select order
     *
     * @param   string $field
     * @param   string $direction
     * @return  Mage_Core_Model_Collection
     */
    public function setOrder($field, $direction = 'desc')
    {
        $direction = (strtoupper($direction)=='ASC') ? 'ASC' : 'DESC';
        $this->_orders[$field] = new Zend_Db_Expr($field.' '.$direction);
    	return $this;
    }
    
    /**
     * Set collection item class name
     *
     * @param   string $className
     * @return  Mage_Core_Model_Collection
     */
    function setItemObjectClass($className)
    {
    	if (!is_subclass_of($className, 'Varien_DataObject')) {
    		Mage::exception($className.' does not extends from Varien_DataObject', 0, 'Mage_Core');
    	}
    	$this->_itemObjectClass = $className;
    	return $this;
    }
    
    /**
     * Render sql select conditions
     *
     * @return  Mage_Core_Model_Collection
     */
    protected function _renderFilters()
    {
        if ($this->_isFiltersRendered) {
        	return $this;
        }
        
        foreach ($this->_filters as $filter) {
            switch ($filter['type']) {
                case 'or' :
                    $condition = $this->_dbModel->getReadConnection()->quoteInto($filter['field'].'=?', $filter['value']);
                    $this->_sqlSelect->orWhere($condition);
                    break;
                case 'string' :
                    $this->_sqlSelect->where($filter['value']);
                    break;
                default:
                    $condition = $this->_dbModel->getReadConnection()->quoteInto($filter['field'].'=?', $filter['value']);
                    $this->_sqlSelect->where($condition);
            }
        }
    	return $this;
    }
    
    /**
     * Render sql select orders
     *
     * @return  Mage_Core_Model_Collection
     */
    protected function _renderOrders()
    {
    	foreach ($this->_orders as $orderExpr) {
    		$this->_sqlSelect->order($orderExpr);
    	}
    	return $this;
    }
    
    /**
     * Render sql select limit
     *
     * @return  Mage_Core_Model_Collection
     */
    protected function _renderLimit()
    {
    	if ($this->_curPage<1) {
    		$this->_curPage=1;
    	}
    	
    	$this->_sqlSelect->limitPage($this->_curPage, $this->_pageSize);
    	return $this;
    }
    
    /**
     * Load data
     *
     * @return  Mage_Core_Model_Collection
     */
    public function loadData()
    {
    	$this->_renderFilters()
    	     ->_renderOrders()
    	     ->_renderLimit();
    	     
    	$data = $this->_dbModel->getReadConnection()->fetchAll($this->_sqlSelect);
    	if (is_array($data)) {
    		foreach ($data as $item) {
    			$this->_items[] = new $this->_itemObjectClass($item);
    		}
    	}
    	return $this;
    }
    
    public function load()
    {
    	return $this->loadData();
    }
    
    /**
     * Convert collection to XML
     *
     * @return string
     */
    public function __toXml()
    {
    	$xml = '<?xml version="1.0" encoding="UTF-8"?>
    	<collection>
    	   <totalRecords>'.$this->_totalRecords.'</totalRecords>
    	   <items>';
    	
    	foreach ($this->_items as $index => $item) {
    		$xml.=$item->__toXml();
    	}
    	$xml.= '</items>
    	</collection>';
    	return $xml;
    }
    
    /**
     * Convert collection to array
     *
     * @return array
     */
    public function __toArray($arrRequiredFields = array())
    {
    	$arrItems = array();
    	$arrItems['totalRecords'] = $this->getSize();
    	
    	$arrItems['items'] = array();    	
    	foreach ($this->_items as $index => $item) {
    		$arrItems['items'][] = $item->__toArray($arrRequiredFields);
    	}
    	return $arrItems;
    }
    
    /**
     * implematation of iterator block
     */
    function current()
    {
        return $this->_items[$this->_counter];
    }

    function next()
    {
        return $this->_counter++;

    }

    function key()
    {
        return $this->_counter;

    }

    function valid()
    {
        return isset($this->_items[$this->_counter]);
    }

    function rewind()
    {
        $this->_counter = 0;
    }
}