<?php

class Mage_Shiptable_Model_Mysql4_Table
{
    protected $_read;
    protected $_write;
    protected $_shipTable;
    
    public function __construct()
    {
        $this->_read = Mage::registry('resources')->getConnection('shiptable_read');
        $this->_write = Mage::registry('resources')->getConnection('shiptable_write');
        $this->_shipTable = Mage::registry('resources')->getTableName('shiptable_resource', 'shiptable');
    }
    
    public function getRate(Mage_Sales_Model_Shipping_Method_Request $request)
    {
        $select = $this->_read->select()->from($this->_shipTable);
        $select->where($this->_read->quoteInto('dest_country_id=?', $request->getDestCountryId()));
        $select->where($this->_read->quoteInto('dest_region_id=?', $request->getDestRegionId()));
        $select->where($this->_read->quoteInto('condition_name=?', $request->getConditionName()));
        $select->where($this->_read->quoteInto('condition_value>?', $request->getData($request->getConditionName())));
        $select->order('condition_value')->limit(1);
        $row = $this->_read->fetchRow($select);
        return $row;
    }
}