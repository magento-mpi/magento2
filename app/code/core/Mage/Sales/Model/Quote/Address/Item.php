<?php

class Mage_Sales_Model_Quote_Address_Item extends Mage_Core_Model_Abstract
{
    protected $_address;
    
    protected function _construct()
    {
        $this->_init('sales/quote_address_item');
    }
    
    public function setAddress(Mage_Sales_Model_Quote_Address $address)
    {
        $this->_address = $address;
        return $this;
    }
    
    public function getAddress()
    {
        return $this->_address;
    }
}