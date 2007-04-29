<?php

class Mage_Paygate_Model_Verisign extends Mage_Sales_Model_Payment_Abstract
{
    public function createFormBlock($name)
    {
        $block = $this->getLayout()->createBlock('payment_cc_form', $name)
            ->assign('method', 'verisign')
            ->init($this->_payment);
        return $block;
    }
    
    public function createInfoBlock($name)
    {
        $block = $this->getLayout()->createBlock('payment_cc_info', $name)
            ->init($this->_payment);
        return $block;
    }
    

}