<?php

class Mage_Sales_Model_Invoice_Shipment extends Mage_Core_Model_Abstract
{
    protected $_invoice;
    
    function _construct()
    {
        $this->_init('sales/invoice_shipment');
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
}