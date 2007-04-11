<?php
/**
 * Base items collection class 
 *
 * @package    Varien
 * @subpackage Data
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 * @author     Andrey Korolyov <andrey@varien.com>
 * @author     Moshe Gurvich <moshe@varien.com>
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Varien_Data_Collection implements Iterator
{
    // ITEMS
    protected $_items = array();
    protected $_itemObjectClass = 'Varien_Data_Object';
    
    // FILTERS AND ORDERS
    protected $_orders      = array();
    protected $_filters     = array();
    protected $_isFiltersRendered = false;
    
    // PAGER
    protected $_curPage     = 1;
    // if pageSize == false, then all data is selected
    protected $_pageSize    = false;
    protected $_totalRecords= null;
    
    // ITERATOR
    protected $_counter = 0;
    
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
        $collectionSize = $this->getSize();
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
    
    public function addItem(Varien_Data_Object $item)
    {
        $this->_items[] = $item;
    }
    
    public function removeItemByKey($key)
    {
        unset($this->_items[$key]);
    }
    
    public function clear()
    {
        $this->_items[] = array();
    }
        
    public function walk($method, $args=array())
    {
        foreach ($this->getItems() as $item) {
            call_user_func_array(array($item, $method), $args);
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
        $this->_curPage = $page;
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
        if (!is_subclass_of($className, 'Varien_Data_Object')) {
            Mage::exception($className.' does not extends from Varien_Data_Object', 0, 'Mage_Core');
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
    
    public function getItemById($idValue)
    {
        return false;
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