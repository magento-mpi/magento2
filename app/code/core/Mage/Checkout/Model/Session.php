<?php

class Mage_Checkout_Model_Session extends Mage_Core_Model_Session_Abstract
{
    protected $_quote = null;
    protected $_processedQuote = null;

    public function __construct()
    {
        $this->init('checkout');
        Mage::dispatchEvent('initCheckoutSession', array('checkout_session'=>$this));
    }

    public function unsetAll()
    {
        parent::unsetAll();
        $this->_quote = null;
    }

    public function getQuote()
    {
        if (empty($this->_quote)) {
            $quote = Mage::getModel('sales/quote');
            if ($this->getQuoteId()) {
                $quote->load($this->getQuoteId());
                if (!$quote->getId()) {
                    $this->setQuoteId(null);
                }
            }
            if (!$this->getQuoteId()) {
                $quote->save();
                Mage::dispatchEvent('initQuote', array('quote'=>$quote));
                $this->setQuoteId($quote->getId());
            }
            if ($this->getQuoteId() && !$quote->getCustomerId()) {
                $customerSession = Mage::getSingleton('customer/session');
                if ($customerSession->isLoggedIn()) {
                    $quote->setCustomerId($customerSession->getCustomerId())->save();
                }
            }
            $this->_quote = $quote;
        }
        return $this->_quote;
    }

    public function getProcessedQuote()
    {
        if (!$this->_processedQuote) {
            $this->_processedQuote = Mage::getModel('sales/quote_processed');
            if ($this->getQuote()->getProcessedQuoteId()) {
                $this->_processedQuote->load($this->getQuote()->getProcessedQuoteId());
            } else {
                $this->_processedQuote->importQuote($this->getQuote())->save();
            }
        }
        return $this->_processedQuote;
    }

    public function loadCustomerQuote()
    {
        $customerId = Mage::getSingleton('customer/session')->getCustomerId();
        $customerQuote = Mage::getModel('sales/quote');
        if ($customerQuote->loadByCustomerId($customerId)) {
            if ($this->getQuoteId()) {
                foreach ($this->getQuote()->getEntitiesByType('item') as $item) {
                    $item->setEntityId(null);
                    $customerQuote->addProduct($item);
                }
                $customerQuote->save();
            }
            $this->setQuoteId($customerQuote->getQuoteId());
            if ($this->_quote) {
                $this->_quote->delete();
            }
            $this->_quote = $customerQuote;
        }
        return $this;
    }

    public function clear()
    {
        Mage::dispatchEvent('destoryQuote', array('quote'=>$quote));
        $this->_quote = null;
        $this->setQuoteId(null);
    }
}
