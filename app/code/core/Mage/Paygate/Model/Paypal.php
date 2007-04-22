<?php

class Mage_Paygate_Model_Paypal extends Mage_Sales_Model_Payment_Abstract
{
    public function createFormBlock($name)
    {
        $block = Mage::createBlock('payment_cc_form', $name)
            ->assign('method', 'paypal')
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