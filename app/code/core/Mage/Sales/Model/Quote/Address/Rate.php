<?php

class Mage_Sales_Model_Quote_Address_Rate extends Mage_Core_Model_Abstract
{
    protected $_address;
    
    protected function _construct()
    {
        $this->_init('sales/quote_address_rate');
    }
    
    public function setAddress(Mage_Core_Model_Quote_Address $address)
    {
        $this->_address = $address;
        return $this;
    }
    
    public function getAddress()
    {
        return $this->_address;
    }
}