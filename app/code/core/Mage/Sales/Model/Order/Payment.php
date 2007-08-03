<?php

class Mage_Sales_Model_Order_Payment extends Mage_Core_Model_Abstract
{
    protected $_order;
    
    protected function _construct()
    {
        $this->_init('sales/order_payment');
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
    
    public function importQuotePayment(Mage_Sales_Model_Quote_Payment $payment)
    {
        return $this;
    }
}