<?php

class Mage_Sales_Model_Payment_PurchaseOrder extends Mage_Sales_Model_Payment_Abstract 
{
    public function createFormBlock($name)
    {        
        $block = $this->getLayout()->createBlock('core/template', $name)
            ->setTemplate('sales/payment/purchaseorder.phtml')
            ->assign('payment', $this->_payment);
        
        return $block;
    }
    
    public function createInfoBlock($name)
    {
        $out = __('Purchase Order')."\n".
            __('PO Number').': '.$this->_payment->getPoNumber();
            
        $block = $this->getLayout()->createBlock('core/text', $name)->setText(nl2br($out));
        
        return $block;
    }
}