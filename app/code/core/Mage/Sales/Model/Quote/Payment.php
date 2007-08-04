<?php

class Mage_Sales_Model_Quote_Payment extends Mage_Core_Model_Abstract
{
    protected $_quote;
    
    function _construct()
    {
        $this->_init('sales/quote_payment');
    }
    
    public function setQuote(Mage_Sales_Model_Quote $quote)
    {
        $this->_quote = $quote;
        return $this;
    }
    
    public function getQuote()
    {
        return $this->_quote;
    }
    
    public function importCustomerPayment(Mage_Customer_Model_Payment $payment)
    {
        $this
            ->setCustomerPaymentId($payment->getId())
            ->setMethod($payment->getMethod())
            ->setCcType($payment->getCcType())
            ->setCcNumberEnc($payment->getCcNumberEnc())
            ->setCcLast4($payment->getCcLast4())
            ->setCcOwner($payment->getCcOwner())
            ->setCcExpMonth($payment->getCcExpMonth())
            ->setCcExpYear($payment->getCcExpYear())
        ;
    }
    
    protected function _beforeSave()
    {
        if ($this->getQuote()) {
            $this->setParentId($this->getQuote()->getId());
        }
        parent::_beforeSave();
    }
}