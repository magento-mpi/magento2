<?php

abstract class Mage_Payment_Model_Abstract extends Varien_Object
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
    
    public function getLayout()
    {
        return Mage::registry('action')->getLayout();
    }
    
    public function onOrderValidate(Mage_Sales_Model_Order_Payment $payment)
    {
        return $this;
    }
    
    public function onInvoiceCreate(Mage_Sales_Model_Invoice_Payment $payment)
    {
        return $this;
    }
}