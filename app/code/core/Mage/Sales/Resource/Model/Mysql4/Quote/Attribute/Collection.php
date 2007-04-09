<?php

class Mage_Sales_Resource_Model_Mysql4_Quote_Attribute_Collection extends Varien_Data_Collection_Db 
{
    static protected $_attributeTable = null;

    public function __construct() 
    {
        parent::__construct(Mage::registry('resources')->getConnection('sales_read'));
        self::$_attributeTable = Mage::registry('resources')->getTableName('sales', 'quote_attribute');
        $this->_sqlSelect->from(self::$_attributeTable);
        $this->setItemObjectClass(Mage::getConfig()->getResourceModelClassName('sales', 'quote_attribute'));
    }
    
    public function loadByQuoteId($quoteId)
    {
        $this->addFilter('quote_id', (int)$quoteId, 'and');
        $this->load();
        return $this;
    }
}