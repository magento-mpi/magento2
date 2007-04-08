<?php

class Mage_Sales_Resource_Model_Mysql4_Quote_Item_Attribute_Collection extends Varien_Data_Collection_Db 
{
    static protected $_attributeTable = null;

    public function __construct() 
    {
        parent::__construct(Mage::getResourceModel('sales')->getReadConnection());
        self::$_attributeTable = Mage::registry('resources')->getTableName('sales', 'quote_item_attribute');
        $this->_sqlSelect->from(self::$_attributeTable);
        $this->setItemObjectClass(Mage::getConfig()->getResourceModelClassName('sales', 'quote_item_attribute'));
    }
    
    public function loadByQuoteId($quoteId)
    {
        $this->addFilter('quote_id', (int)$quoteId, 'and');
        $this->load();
        return $this;
    }
        
    public function loadByQuoteItemId($quoteItemId)
    {
        $this->addFilter('quote_item_id', (int)$quoteItemId, 'and');
        $this->load();
        return $this;
    }
}