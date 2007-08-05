<?php

class Mage_Shipping_Model_Mysql4_Carrier_Tablerate
{
    protected $_read;
    protected $_write;
    protected $_shipTable;
    
    public function __construct()
    {
        $this->_read = Mage::getSingleton('core/resource')->getConnection('shipping_read');
        $this->_write = Mage::getSingleton('core/resource')->getConnection('shipping_write');
        $this->_shipTable = Mage::getSingleton('core/resource')->getTableName('shipping/tablerate');
    }
    
    public function getRate(Mage_Shipping_Model_Rate_Request $request)
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