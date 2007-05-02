<?php

class Mage_Checkout_Model_Session extends Mage_Core_Model_Session_Abstract
{
    protected $_quote = null;
    
    public function __construct()
    {
        $this->init('checkout');
    }

    public function unsetAll()
    {
        parent::unsetAll();
        $this->_quote = null;
    }
    
    public function getQuote()
    {
        if (empty($this->_quote)) {
            $quote = Mage::getModel('sales', 'quote');
            if ($this->getQuoteId()) {
                $quote->load($this->getQuoteId());
                if (!$quote->getId()) {
                    $this->setQuoteId(null);
                }
            }
            if (!$this->getQuoteId()) {
                $quote->save();
                $this->setQuoteId($quote->getId());
            }
            if ($this->getQuoteId() && !$quote->getCustomerId()) {
                $customerSession = Mage::getSingleton('customer', 'session');
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
        $customerId = Mage::getSingleton('customer', 'session')->getCustomerId();
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

    public function clear()
    {
        $this->_quote = null;
    }
}
