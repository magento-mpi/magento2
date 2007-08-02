<?php

class Mage_Sales_Model_Quote_Address extends Mage_Core_Model_Abstract
{
    function _construct()
    {
        $this->_init('sales/quote_address');
    }
    
    public function collectTotals()
    {
        $this->getResource()->collectTotals($this);
        return $this;
    }
    
    public function getTotals()
    {
        return $this->getResource()->getTotals($this);
    }
}