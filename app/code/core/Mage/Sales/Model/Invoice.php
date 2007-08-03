<?php

class Mage_Sales_Model_Invoice extends Mage_Core_Model_Abstract
{
    protected function _construct()
    {
        $this->_init('sales/invoice');
    }
    
    public function createFromOrder(Mage_Sales_Model_Order $order)
    {
        return $this;
    }
}