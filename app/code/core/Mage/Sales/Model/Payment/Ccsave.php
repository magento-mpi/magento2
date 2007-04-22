<?php

class Mage_Sales_Model_Payment_Ccsave extends Mage_Sales_Model_Payment_Abstract 
{
    public function createFormBlock($name)
    {
        $block = Mage::createBlock('payment_cc_form', $name)
            ->assign('method', 'ccsave')
            ->init($this->_payment);
        return $block;
    }
    
    public function createInfoBlock($name)
    {
        $block = Mage::createBlock('payment_cc_info', $name)
            ->init($this->_payment);
        return $block;
    }
}