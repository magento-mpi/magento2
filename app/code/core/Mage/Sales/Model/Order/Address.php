<?php

class Mage_Sales_Model_Order_Address extends Mage_Core_Model_Abstract
{
    protected $_order;
    
    protected function _construct()
    {
        $this->_init('sales/order_address');
    }
    
    public function setOrder(Mage_Sales_Model_Order $order)
    {
        $this->_order = $order;
        return $this;
    }
    
    public function getOrder()
    {
        return $this->_order;
    }
    
    public function importQuoteAddress(Mage_Sales_Model_Quote_Address $newAddress)
    {
        $address = clone $newAddress;
        $address->unsEntityId()
            ->unsAttributeSetId()
            ->unsEntityTypeId()
            ->unsParentId();
            
        $this->addData($address->getData());
        return $this;
    }
}