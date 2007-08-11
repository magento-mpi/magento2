<?php

class Mage_Sales_Model_Order_Payment extends Mage_Core_Model_Abstract
{
    protected $_order;
    
    protected function _construct()
    {
        $this->_init('sales/order_payment');
    }
        
    public function setOrder(Mage_Sales_Model_Order $order)
    {
        $this->_order = $order;
        return $this;
    }
    
    public function getOrder()
    {
        return $this->_order;
    }
    
    public function importQuotePayment(Mage_Sales_Model_Quote_Payment $newPayment)
    {
        $payment = clone $newPayment;
        $payment->unsEntityId()
            ->unsAttributeSetId()
            ->unsEntityTypeId()
            ->unsParentId();
            
        $this->addData($payment->getData());
        $this->setAmount($payment->getQuote()->getGrandTotal());
        return $this;
    }
    
    public function getCcNumber()
    {
        if (!$this->getData('cc_number') && $this->getData('cc_number_enc')) {
            $customerPayment = Mage::getModel('customer/payment');
            $this->setData('cc_number', $customerPayment->decrypt($this->getData('cc_number_enc')));
        }
        return $this->getData('cc_number');
    }
    
    public function getCcCid()
    {
        if (!$this->getData('cc_cid') && $this->getData('cc_cid_enc')) {
            $customerPayment = Mage::getModel('customer/payment');
            $this->setData('cc_cid', $customerPayment->decrypt($this->getData('cc_cid_enc')));
        }
        return $this->getData('cc_cid');
    }
}