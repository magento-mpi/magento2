<?php

class Mage_Customer_Resource_Model_Mysql4_Address_Collection extends Varien_Data_Collection_Db
{
    protected $_addressTable = null;

    public function __construct() 
    {
        parent::__construct(Mage::getResourceModel('customer')->getReadConnection());
        
        $this->_addressTable = Mage::registry('resources')->getTableName('customer', 'address');

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
    
    public function load($printQuery = false, $logQuery = false)
    {
        // load addresses data
        $data = parent::load($printQuery, $logQuery);
        
        // if empty return
        if (empty($data)) {
            return $data;
        }
        
        // collect address ids
        $addressIds = array();
        foreach ($this->_items as $item) {
            $addressIds[] = $item->getAddressId();
        }
        
        // fetch all types for collection addresses
        $condition = $this->getConnection()->quoteInto("address_id in (?)", $addressIds);
        $typesArr = Mage::getResourceModel('customer', 'address_type')->getCollection($condition);
        
        // process result
        $types = array('primary_types'=>array(), 'alternative_types'=>array());
        foreach ($typesArr as $type) {
            $types[$type['address_id']][$type['address_type_code']] = array('is_primary'=>$type['is_primary']);
        }
       
        // set types to address objects and explode street address
        foreach ($this->_items as $item) {
            $item->setType($types[$item->getAddressId()]);
        }
    }
}