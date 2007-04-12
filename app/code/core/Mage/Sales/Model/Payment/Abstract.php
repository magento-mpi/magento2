<?php

abstract class Mage_Sales_Model_Payment_Abstract
{
    protected $_payment = null;
    
    public function createBlock($name) 
    {
        return false;
    }
    
    public function setPayment(Mage_Customer_Model_Payment $payment)
    {
        $this->_payment = $payment;
    }
    
    public function getPayment()
    {
        return $this->_payment;
    }
}