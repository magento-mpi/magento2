<?php

class Mage_Customer_Resource_Model_Mysql4_Address_Collection extends Mage_Core_Resource_Model_Db_Collection
{
    protected $_addressTable;

    public function __construct() 
    {
        parent::__construct(Mage::getResourceModel('customer'));
        
        $this->_addressTable    = $this->_dbModel->getTableName('customer', 'address');
        $this->_sqlSelect->from($this->_addressTable);
        
        $this->setItemObjectClass('Mage_Customer_Address');
    }
    
    public function filterByCustomerId($customerId)
    {
        $this->addFilter('customer_id', (int)$customerId, 'and');
        return $this;
    }
    
    public function filterByCondition($condition)
    {
        $this->addFilter('', $condition, 'string');
        return $this;
    }
}