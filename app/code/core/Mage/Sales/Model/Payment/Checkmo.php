<?php

class Mage_Sales_Model_Payment_Checkmo extends Mage_Sales_Model_Payment_Abstract 
{
    public function createFormBlock($name)
    {        
        $block = Mage::createBlock('tpl', $name)
            ->setTemplate('sales/payment/checkmo.phtml')
            ->assign('payment', $this->_payment);
        
        return $block;
    }
    
    public function createInfoBlock($name)
    {
        $out = __('Check / MO');
            
        $block = Mage::createBlock('text', $name)->setText(nl2br($out));
        
        return $block;
    }
}