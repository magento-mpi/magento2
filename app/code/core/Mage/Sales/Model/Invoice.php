<?php

class Mage_Sales_Model_Invoice extends Mage_Sales_Model_Document 
{
    protected function _setDocumentProperties()
    {
        $this->_docType = 'invoice';
    }
    
    public function getEntityTemplates()
    {
        return array(
            'item'=>Mage::getModel('sales/invoice_entity_item'),
            'address'=>Mage::getModel('sales/invoice_entity_address'),
            'payment'=>Mage::getModel('sales/invoice_entity_payment'),
            'shipment'=>Mage::getModel('sales/invoice_entity_shipment'),
        );
    }
}