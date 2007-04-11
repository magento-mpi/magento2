<?php

class Mage_Checkout_Model_Session extends Varien_Data_Object
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
            if (!$quote->getQuoteId()) {
                $this->setQuoteId(null);
            }
        }
        if ($this->getQuoteId() && !$quote->getCustomerId()) {
            $customerSession = Mage::getSingleton('customer_model', 'session');
            if ($customerSession->isLoggedIn()) {
                $quote->setCustomerId($customerSession->getCustomerId())->save();
            }
        }
        return $quote;
    }
    
    public function setData($var, $value='', $isChanged=true)
    {
        $this->_session->$var = $value;
    }
    
    public function getData($var='')
    {
        return $this->_session->$var;
    }
}
