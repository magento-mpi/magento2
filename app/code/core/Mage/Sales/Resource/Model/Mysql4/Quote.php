<?php

class Mage_Sales_Resource_Model_Mysql4_Quote extends Mage_Sales_Quote
{
    protected static $_read;
    protected static $_write;
    
    public function __construct($data=array())
    {
        self::$_read = Mage::registry('resources')->getConnection('sales', 'read');
        self::$_write = Mage::registry('resources')->getConnection('sales', 'write');
    }
    
    public function loadByQuoteId($quoteId)
    {
        $quoteTable = Mage::registry('resources')->getTableName('sales', 'quote');
        $select = self::$_read->select()->from($quoteTable)
            ->where(self::$_read->quoteInto("quote_id=?", $quoteId));
        $this->setData(self::$_read->fetchRow($select));
        
        $this->loadAttributes();
    }
    
    public function loadAttributes()
    {
        $attrTable = Mage::registry('resources')->getTableName('sales', 'quote_attribute');
        $select = self::$_read->select()->from($attrTable)
            ->where(self::$_read->quoteInto("quote_id=?", $this->getQuoteId()));
        $attributes = self::$_read->fetchRow($select);
        if (empty($attributes)) {
            return false;
        }
        foreach ($attributes as $attr) {
            
        }
    }
}