<?php

class Mage_Paygate_Model_Payflow_Pro extends Mage_Payment_Model_Abstract
{
    public function createFormBlock($name)
    {
        $block = $this->getLayout()->createBlock('payment/form_cc', $name)
            ->setMethod('verisign')
            ->setPayment($this->getPayment());
        return $block;
    }
    
    public function createInfoBlock($name)
    {
        $block = $this->getLayout()->createBlock('payment/info_cc', $name)
            ->setPayment($this->getPayment());
        return $block;
    }

}
