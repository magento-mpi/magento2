<?php

class Mage_Payment_Model_Ccsave extends Mage_Payment_Model_Abstract 
{
    public function createFormBlock($name)
    {
        $block = $this->getLayout()->createBlock('payment/form_cc', $name)
            ->setPayment($this->getPayment())
            ->setMethod('ccsave');
        return $block;
    }
    
    public function createInfoBlock($name)
    {
        $block = $this->getLayout()->createBlock('payment/info_cc', $name)
            ->setPayment($this->getPayment());
        return $block;
    }
    
    public function onOrderValidate(Mage_Sales_Model_Order_Payment $payment)
    {
        $payment->setStatus('APPROVED');
        $payment->getOrder()->addStatus(Mage::getStoreConfig('payment/ccsave/order_status'));
        return $this;
    }
}