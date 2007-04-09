<?php

/**
 * Base items collection class 
 *
 * @package    Mage
 * @subpackage Core
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 * @author     Andrey Korolyov
 * @author     Moshe Gurvich <moshe@varien.com>
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Varien_Data_Collection_Db extends Varien_Data_Collection
{

    // DB connection
    protected $_conn;
    
    /**
     * Select oblect
     *
     * @var Zend_Db_Select
     */
    protected $_sqlSelect;
    
    public function __construct($conn=null)
    {
        parent::__construct();
        
        if (!is_null($conn)) {
            $this->setConnection($conn);
        }
    }
    
    public function setConnection($conn)
    {
        if (!$conn instanceof Zend_Db_Adapter_Abstract) {
            throw new Zend_Exception('dbModel read resource does not implement Zend_Db_Adapter_Abstract');
        }

        $this->_conn = $conn;
        $this->_sqlSelect = $this->_conn->select();
    }
    
    public function getConnection()
    {
        return $this->_conn;
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
            $this->_totalRecords = $this->_conn->fetchOne($sql);
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
     * Set select order
     *
     * @param   string $field
     * @param   string $direction
     * @return  Varien_Data_Collection_Db
     */
    public function setOrder($field, $direction = 'desc')
    {
        $direction = (strtoupper($direction)=='ASC') ? 'ASC' : 'DESC';
        $this->_orders[$field] = new Zend_Db_Expr($field.' '.$direction);
        return $this;
    }
    
    /**
     * Render sql select conditions
     *
     * @return  Varien_Data_Collection_Db
     */
    protected function _renderFilters()
    {
        if ($this->_isFiltersRendered) {
            return $this;
        }
        
        foreach ($this->_filters as $filter) {
            switch ($filter['type']) {
                case 'or' :
                    $condition = $this->_conn->quoteInto($filter['field'].'=?', $filter['value']);
                    $this->_sqlSelect->orWhere($condition);
                    break;
                case 'string' :
                    $this->_sqlSelect->where($filter['value']);
                    break;
                default:
                    $condition = $this->_conn->quoteInto($filter['field'].'=?', $filter['value']);
                    $this->_sqlSelect->where($condition);
            }
        }
        return $this;
    }
    
    /**
     * Render sql select orders
     *
     * @return  Varien_Data_Collection_Db
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
     * @return  Varien_Data_Collection_Db
     */
    protected function _renderLimit()
    {
        if ($this->_curPage<1) {
            $this->_curPage=1;
        }
        
        if($this->_pageSize){
            $this->_sqlSelect->limitPage($this->_curPage, $this->_pageSize);
        }
        
        return $this;
    }
    
    /**
     * Set select distinct
     *
     * @param bool $flag
     */
    public function distinct($flag)
    {
        $this->_sqlSelect->distinct($flag);
    }
    
    /**
     * Load data
     *
     * @return  Varien_Data_Collection_Db
     */
    public function loadData($printQuery = false, $logQuery = false)
    {
        $this->_renderFilters()
             ->_renderOrders()
             ->_renderLimit();

        if($printQuery) {
            echo $this->_sqlSelect->__toString();
        }
        
        if($logQuery){
            Mage::log($this->_sqlSelect->__toString());
        }

        $data = $this->_conn->fetchAll($this->_sqlSelect);
        if (is_array($data)) {
            foreach ($data as $item) {
                $this->addItem(new $this->_itemObjectClass($item));
            }
        }
        return $this;
    }

}