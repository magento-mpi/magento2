<?php

class Mage_Checkout_Model_Session extends Varien_Data_Object
{
    protected $_session = null;
    protected $_quote = null;
    
    public function __construct()
    {
        $this->_session = new Zend_Session_Namespace('checkout', Zend_Session_Namespace::SINGLE_INSTANCE);
    }
    
    public function setData($var, $value='', $isChanged=true)
    {
        $this->_session->$var = $value;
    }
    
    public function getData($var='', $index=false)
    {
        return $this->_session->$var;
    }

    public function unsetAll()
    {
        $this->_session->unsetAll();
        $this->_quote = null;
    }
    
    public function getQuote()
    {
        if (empty($this->_quote)) {
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
            $this->_quote = $quote;
        }
        return $this->_quote;
    }

    public function loadCustomerQuote()
    {
        $customerId = Mage::getSingleton('customer_model', 'session')->getCustomerId();
        $customerQuote = Mage::getModel('sales', 'quote');
        if ($customerQuote->loadByCustomerId($customerId)) {
            if ($this->getQuoteId()) {
                foreach ($this->getQuote()->getEntitiesByType('item') as $item) {
                    $customerQuote->addProduct($item->setEntityId(null));
                }
                $customerQuote->save();
            }
            $this->setQuoteId($customerQuote->getQuoteId());
            $this->_quote = $customerQuote;        
        }
        return $this;
    }
}
