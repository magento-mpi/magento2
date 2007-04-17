<?php

class Mage_Sales_Model_Order extends Mage_Sales_Model_Document 
{
    protected function _setDocumentProperties()
    {
        $this->_docType = 'order';
    }
    
    public function getEntityTemplates()
    {
        return array(
            'item'=>Mage::getModel('sales', 'order_entity_item'),
            'address'=>Mage::getModel('sales', 'order_entity_address'),
            'payment'=>Mage::getModel('sales', 'order_entity_payment'),
            'status'=>Mage::getModel('sales', 'order_entity_status'),
        );
    }
    
}