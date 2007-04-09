<?php

class Mage_Shiptable_Model_Mysql4_Table extends Mage_Shiptable_Model_Table
{
    protected static $_read;
    protected static $_write;
    protected static $_shipTable;
    
    public function __construct()
    {
        self::$_read = Mage::registry('resources')->getConnection('shiptable_read');
        self::$_write = Mage::registry('resources')->getConnection('shiptable_write');
        self::$_shipTable = Mage::registry('resources')->getTableName('shiptable', 'shiptable');
    }
    
    public function getRate(Mage_Sales_Model_Shipping_Quote_Request $request)
    {
        $select = self::$_read->select()->from(self::$_shipTable);
        $select->where(self::$_read->quoteInto('dest_country_id=?', $request->getDestCountryId()));
        $select->where(self::$_read->quoteInto('dest_region_id=?', $request->getDestRegionId()));
        $select->where(self::$_read->quoteInto('condition_name=?', $request->getConditionName()));
        $select->where(self::$_read->quoteInto('condition_value>?', $request->getData($request->getConditionName())));
        $select->order('condition_value')->limit(1);
        $row = self::$_read->fetchRow($select);
        return $row;
    }
}