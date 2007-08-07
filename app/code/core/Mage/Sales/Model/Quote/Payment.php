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
    
    public function importPostData(array $data)
    {
        $payment = Mage::getModel('customer/payment')->setData($data);
        $this
            ->setMethod($payment->getMethod())
            ->setCcType($payment->getCcType())
            ->setCcOwner($payment->getCcOwner())
            ->setCcNumberEnc($payment->encrypt($payment->getCcNumber()))
            ->setCcLast4(substr($payment->getCcNumber(), -4))
            ->setCcExpMonth($payment->getCcExpMonth())
            ->setCcExpYear($payment->getCcExpYear())
            ->setCcCid($payment->encrypt($payment->getCcCid()));
        
        if (!$this->getCcType()) {
            $types = array(3=>__('American Express'), 4=>__('Visa'), 5=>__('Master Card'), 6=>__('Discover'));
            if (isset($types[(int)substr($payment->getCcNumber(),0,1)])) {
                $this->setCcType($types[(int)substr($payment->getCcNumber(),0,1)]);
            }
        }
        
        return $this;
    }
}