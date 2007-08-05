<?php

abstract class Mage_Sales_Model_Payment_Abstract
{
    protected $_payment = null;
    
    public function __construct()
    {
        
    }
    
    public function createFormBlock($name) 
    {
        return false;
    }
    
    public function createInfoBlock($name) 
    {
        return false;
    }
    
    public function setPayment($payment)
    {
        $this->_payment = $payment;
        return $this;
    }
    
    public function getPayment()
    {
        return $this->_payment;
    }
    
    public function getLayout()
    {
        return Mage::registry('action')->getLayout();
    }
    
    public function onOrderValidate(Mage_Sales_Model_Order_Entity_Payment $payment)
    {
        return $this;
    }
    
    public function onInvoiceCreate(Mage_Sales_Model_Invoice_Entity_Payment $payment)
    {
        return $this;
    }
}