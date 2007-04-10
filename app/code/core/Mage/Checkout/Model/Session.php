<?php

class Mage_Checkout_Model_Session
{
    protected $_session = null;
    
    public function __construct()
    {
        $this->_session = new Zend_Session_Namespace('checkout', Zend_Session_Namespace::SINGLE_INSTANCE);
    }
    
    public function setQuoteId($quoteId)
    {
        $this->_session->quoteId = $quoteId;
        return $this;
    }    
    
    public function getQuoteId()
    {
        return $this->_session->quoteId;
    }
    
    public function getQuote()
    {
        $quote = Mage::getModel('sales', 'quote');
        if ($this->getQuoteId()) {
            $quote->load($this->getQuoteId());
        }
        if (!$quote->getCustomerId()) {
            $customerSession = Mage::getSingleton('customer_model', 'session');
            if ($customerSession->isLoggedIn()) {
                $quote->setCustomer($customerSession->getCustomer());
            }
        }
        return $quote;
    }
}
