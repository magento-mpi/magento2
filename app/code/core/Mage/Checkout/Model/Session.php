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
                    $quote->initNewQuote();
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

    public function loadCustomerQuote()
    {
        // coment until quote fix
        $customerId = Mage::getSingleton('customer/session')->getCustomerId();
        $customerQuote = Mage::getModel('sales/quote');
        if ($customerQuote->loadByCustomerId($customerId)) {
            if ($this->getQuoteId()) {
                foreach ($this->getQuote()->getAllItems() as $item) {
                    $customerQuote->addItem($item);
                }
                $customerQuote->save();
            }
            $this->setQuoteId($customerQuote->getId());
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
