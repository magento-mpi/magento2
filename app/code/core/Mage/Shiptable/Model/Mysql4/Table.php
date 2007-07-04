<?php

class Mage_Shiptable_Model_Mysql4_Table
{
    protected $_read;
    protected $_write;
    protected $_shipTable;
    
    public function __construct()
    {
        $this->_read = Mage::getSingleton('core/resource')->getConnection('shiptable_read');
        $this->_write = Mage::getSingleton('core/resource')->getConnection('shiptable_write');
        $this->_shipTable = Mage::getSingleton('core/resource')->getTableName('shiptable/shiptable');
    }
    
    public function getRate(Mage_Sales_Model_Shipping_Method_Request $request)
    {
        $select = $this->_read->select()->from($this->_shipTable);
        $select->where('dest_country_id=?', $request->getDestCountryId());
        $select->where('dest_region_id=?', $request->getDestRegionId());
        $select->where('condition_name=?', $request->getConditionName());
        $select->where('condition_value>?', $request->getData($request->getConditionName()));
        $select->order('condition_value')->limit(1);
        $row = $this->_read->fetchRow($select);
        return $row;
    }
}