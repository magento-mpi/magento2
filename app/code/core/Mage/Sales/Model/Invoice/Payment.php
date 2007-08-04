<?php

class Mage_Sales_Model_Invoice_Payment extends Mage_Core_Model_Abstract
{
    protected $_invoice;
    
    function _construct()
    {
        $this->_init('sales/invoice_transaction');
    }
    
    public function setInvoice(Mage_Sales_Model_Invoice $invoice)
    {
        $this->_invoice = $invoice;
        return $this;
    }
    
    public function getInvoice()
    {
        return $this->_invoice;
    }
    
    public function importOrderPayment(Mage_Sales_Model_Order_Payment $payment)
    {
        return $this;
    }
    
    protected function _beforeSave()
    {
        if ($this->getInvoice()) {
            $this->setParentId($this->getInvoice()->getId());
        }
        parent::_beforeSave();
    }
}