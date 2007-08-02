<?php

class Mage_Sales_Model_Quote_Item extends Mage_Core_Model_Abstract
{
    protected $_quote;
    
    function _construct()
    {
        $this->_init('sales/quote_item');
    }
    
    public function setQuote(Mage_Core_Model_Quote $quote)
    {
        $this->_quote = $quote;
        return $this;
    }
    
    public function getQuote()
    {
        return $this->_quote;
    }
}