<?php

class Mage_Sales_Resource_Model_Mysql4_Quote extends Mage_Sales_Quote
{
    protected static $_read;
    protected static $_write;
    protected static $_quoteTable;
    
    public function __construct($data=array())
    {
        self::$_read = Mage::registry('resources')->getConnection('sales', 'read');
        self::$_write = Mage::registry('resources')->getConnection('sales', 'write');
    }
    
    public function loadByQuoteId($quoteId)
    {
        $select = self::$_read->select()->from(self::$_quoteTable)
            ->where(self::$_read->quoteInto("quote_id=?", $quoteId));
        $this->setData(self::$_read->fetchRow($select));
    }

    
}