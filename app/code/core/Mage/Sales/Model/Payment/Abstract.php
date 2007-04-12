<?php

abstract class Mage_Sales_Model_Payment_Abstract
{
    protected $_payment = null;
    
    public function createFormBlock($name) 
    {
        return false;
    }
    
    public function createInfoBlock($name) 
    {
        return false;
    }
    
    public function setPayment(Mage_Customer_Model_Payment $payment)
    {
        $this->_payment = $payment;
        return $this;
    }
    
    public function getPayment()
    {
        return $this->_payment;
    }
}