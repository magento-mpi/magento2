<?php

class Mage_Sales_Quote extends Varien_Data_Object
{
    protected $_items = null;
    protected $_addresses = null;
    protected $_totals = null;
    
    public function loadAddresses()
    {
        $this->_addresses = Mage::getResourceModel('sales', 'quote_address_collection');
        $this->_addresses->loadByQuoteId($this->getQuoteId());
    }
    
    public function loadItems()
    {
        $this->_items = Mage::getResourceModel('sales', 'quote_item_collection');
        $this->_items->loadByQuoteId($this->getQuoteId());
    }
    
    public function loadTotals()
    {
        $this->_totals = Mage::getResourceModel('sales', 'quote_total_collection');
        $this->_totals->loadByQuoteId($this->getQuoteId());
    }
}