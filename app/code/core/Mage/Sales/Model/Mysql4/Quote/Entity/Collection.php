<?php

class Mage_Sales_Model_Mysql4_Quote_Entity_Collection extends Varien_Data_Collection_Db 
{
    static protected $_entityTable = null;

    public function __construct() 
    {
        parent::__construct(Mage::registry('resources')->getConnection('sales_read'));
        self::$_entityTable = Mage::registry('resources')->getTableName('sales', 'quote_entity');
        $this->_sqlSelect->from(self::$_entityTable);
        $this->setItemObjectClass(Mage::getConfig()->getModelClassName('sales', 'quote_entity'));
    }
    
    public function loadByQuoteId($quoteId)
    {
        $this->addFilter('quote_id', (int)$quoteId, 'and');
        $this->load();
        return $this;
    }
}