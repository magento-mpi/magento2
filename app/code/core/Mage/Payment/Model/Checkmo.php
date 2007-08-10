<?php

class Mage_Payment_Model_Checkmo extends Mage_Payment_Model_Abstract 
{
    public function createFormBlock($name)
    {        
        $block = $this->getLayout()->createBlock('payment/form', $name)
            ->setMethod('checkmo')
            ->setPayment($this->getPayment())
            ->setTemplate('payment/form/checkmo.phtml');
        
        return $block;
    }
    
    public function createInfoBlock($name)
    {
        $block = $this->getLayout()->createBlock('payment/info', $name)
            ->setPayment($this->getPayment());
        
        return $block;
    }
}