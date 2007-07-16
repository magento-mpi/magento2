<?php
/**
 * Data collection
 *
 * @package    Varien
 * @subpackage Data
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 * @author     Andrey Korolyov <andrey@varien.com>
 * @author     Moshe Gurvich <moshe@varien.com>
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Varien_Data_Collection implements IteratorAggregate
{
    /**
     * Collection items
     *
     * @var array
     */
    protected $_items = array();
    
    /**
     * Item object class name
     *
     * @var string
     */
    protected $_itemObjectClass = 'Varien_Object';
    
    /**
     * Order configuration
     *
     * @var array
     */
    protected $_orders      = array();
    
    /**
     * Filters configuration
     *
     * @var array
     */
    protected $_filters     = array();
    
    /**
     * Filter rendered flag
     *
     * @var bool
     */
    protected $_isFiltersRendered = false;
    
    /**
     * Current page number for items pager
     *
     * @var int
     */
    protected $_curPage     = 1;
    
    /**
     * Pager page size
     * 
     * if page size is false, then we works with all items
     *
     * @var int || false
     */
    protected $_pageSize    = false;
    
    /**
     * Total items number
     *
     * @var unknown_type
     */
    protected $_totalRecords= null;
    
    public function __construct() 
    {

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
        $collectionSize = (int) $this->getSize();
        if (0 === $collectionSize) {
            return 1;
        } 
        elseif($this->_pageSize) {
            return ceil($collectionSize/$this->_pageSize);
        }
        else{
            return 1;
        }
    }
    
    public function getPageSize()
    {
        return $this->_pageSize;
    }
    
    /**
     * Get collection size
     *
     * @return int
     */
    public function getSize()
    {
        return $this->_totalRecords;
    }

    public function getFirstItem()
    {
        if(isset($this->_items[0]))
        {
            return $this->_items[0];
        }

        return new $this->_itemObjectClass();
    }

    public function getItems()
    {
        return $this->_items;
    }
    
    public function getColumnValues($colName)
    {
        $col = array();
        foreach ($this->getItems() as $item) {
            $col[] = $item->getData($colName);
        }
        return $col;
    }
    
    public function getItemsByColumnValue($column, $value)
    {
        $res = array();
        foreach ($this as $item) {
        	if ($item->getData($column)==$value) {
        	    $res[] = $item;
        	}
        }
        return $res;
    }
    
    public function getItemByColumnValue($column, $value)
    {
        foreach ($this as $item) {
        	if ($item->getData($column)==$value) {
        	    return $item;
        	}
        }
        return null;
    }
    
    public function addItem(Varien_Object $item)
    {
        $this->_items[] = $item;
    }
    
    public function removeItemByKey($key)
    {
        if (isset($this->_items[$key])) {
            unset($this->_items[$key]);
        }
    }
    
    public function clear()
    {
        $this->_items = array();
    }
        
    public function walk($method, $args=array())
    {
        foreach ($this->getItems() as $item) {
            call_user_func_array(array($item, $method), $args);
        }
    }

    public function each($obj_method, $args=array())
    {
        foreach ($args->_items as $k => $item) {
            $args->_items[$k] = call_user_func($obj_method, $item);
        }
    }

    public function setDataToAll($key, $value=null)
    {
        if (is_array($key)) {
            foreach ($key as $k=>$v) {
                $this->setDataToAll($k, $v);
            }
            return $this;
        }
        foreach ($this->getItems() as $item) {
            $item->setData($key, $value);
        }
        return $this;
    }
    
    /**
     * Set current page
     *
     * @param   int $page 
     * @return  Varien_Data_Collection
     */
    public function setCurPage($page)
    {
        if ($page <= $this->getLastPageNumber()) {
            $this->_curPage = $page;
        }
        else {
            $this->_curPage = 1;
        }
        
        return $this;
    }
    
    /**
     * Set collection page size
     *
     * @param   int $size
     * @return  Varien_Data_Collection
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
     * @return  Varien_Data_Collection
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
     * @return  Varien_Data_Collection
     */
    function setItemObjectClass($className)
    {
        if (!is_subclass_of($className, 'Varien_Object')) {
            throw new Exception($className.' does not extends from Varien_Object');
        }
        $this->_itemObjectClass = $className;
        return $this;
    }
    
    /**
     * Render sql select conditions
     *
     * @return  Varien_Data_Collection
     */
    protected function _renderFilters()
    {
        return $this;
    }
    
    /**
     * Render sql select orders
     *
     * @return  Varien_Data_Collection
     */
    protected function _renderOrders()
    {
        return $this;
    }
    
    /**
     * Render sql select limit
     *
     * @return  Varien_Data_Collection
     */
    protected function _renderLimit()
    {
        return $this;
    }
    
    /**
     * Set select distinct
     *
     * @param bool $flag
     */
    public function distinct($flag)
    {
        return $this;
    }
    
    /**
     * Load data
     *
     * @return  Varien_Data_Collection
     */
    public function loadData($printQuery = false, $logQuery = false)
    {
        return $this;
    }
    
    /**
     * Load data
     *
     * @return  Varien_Data_Collection
     */
    public function load($printQuery = false, $logQuery = false)
    {
        return $this->loadData($printQuery, $logQuery);
    }
    
    /**
     * Convert collection to XML
     *
     * @return string
     */
    public function toXml()
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>
        <collection>
           <totalRecords>'.$this->_totalRecords.'</totalRecords>
           <items>';
        
        foreach ($this->_items as $index => $item) {
            $xml.=$item->toXml();
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
    public function toArray($arrRequiredFields = array())
    {
        $arrItems = array();
        $arrItems['totalRecords'] = $this->getSize();
        
        $arrItems['items'] = array();       
        foreach ($this->_items as $index => $item) {
            $arrItems['items'][] = $item->toArray($arrRequiredFields);
        }
        return $arrItems;
    }
    
    /**
     * Convert items array to array for select options
     * 
     * return items array
     * array(
     *      $index => array(
     *          'value' => mixed
     *          'label' => mixed
     *      )
     * )
     * 
     * @param   string $valueField
     * @param   string $labelField
     * @return  array
     */
    protected function _toOptionArray($valueField='id', $labelField='name', $additional=array())
    {
        $res = array();
        $additional['value'] = $valueField;
        $additional['label'] = $labelField;
        
        foreach ($this as $item) {
            foreach ($additional as $code => $field) {
                $data[$code] = $item->getData($field);
            }
        	$res[] = $data;
        }
        return $res;
    }
    
    public function toOptionArray()
    {
        return $this->_toOptionArray();
    }
    
    public function getItemById($idValue)
    {
        foreach ($this as $item) {
        	if ($item->getId()==$idValue) {
        	    return $item;
        	}
        }
        return false;
    }
    
    /**
     * Implementation of IteratorAggregate::getIterator()
     */
    public function getIterator()
    {
        return new ArrayIterator($this->_items);
    }
}
