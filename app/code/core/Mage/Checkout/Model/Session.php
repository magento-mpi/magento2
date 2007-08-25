<?php

class Mage_Checkout_Model_Session extends Mage_Core_Model_Session_Abstract
{
    const CHECKOUT_STATE_BEGIN = 'begin';
    
    protected $_quote = null;
    protected $_processedQuote = null;

    public function __construct()
    {
        $this->init('checkout');
        #echo $this->getSessionId().';'.$this->_namespace->data->getQuoteId().';'.$this->getQuoteId();
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
                $quote->initNewQuote()->save();
                Mage::dispatchEvent('initQuote', array('quote'=>$quote));
                $this->setQuoteId($quote->getId());
            }
            if ($this->getQuoteId() && !$quote->getCustomerId()) {
                $customerSession = Mage::getSingleton('customer/session');
                if ($customerSession->isLoggedIn()) {
                    $quote->setCustomer($customerSession->getCustomer())->save();
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
        $customerQuote = Mage::getResourceModel('sales/quote_collection')
            ->loadByCustomerId($customerId);
        if ($customerQuote) {
            if ($this->getQuoteId()) {
                foreach ($this->getQuote()->getAllItems() as $item) {
                    $item->setId(null);
                    $found = false;
		            foreach ($customerQuote->getAllItems() as $quoteItem) {
		                if ($quoteItem->getProductId()==$item->getProductId()) {
		                    $quoteItem->setQty($quoteItem->getQty() + $item->getQty());
		                    $found = true;
		                    break;
		                }
		            }
		            if (!$found) {
                    	$customerQuote->addItem($item);
		            }
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
    
    public function setStepData($step, $data, $value=null)
    {
        $steps = $this->getSteps();
        if (is_null($value)) {
            if (is_array($data)) {
                $steps[$step] = $data;
            }
        } else {
            if (!isset($steps[$step])) {
                $steps[$step] = array();
            }
            if (is_string($data)) {
                $steps[$step][$data] = $value;
            }
        }
        $this->setSteps($steps);
        
        return $this;
    }
    
    public function getStepData($step=null, $data=null)
    {
        $steps = $this->getSteps();
        if (is_null($step)) {
            return $steps;
        }
        if (!isset($steps[$step])) {
            return false;
        }
        if (is_null($data)) {
            return $steps[$step];
        }
        if (!is_string($data) || !isset($steps[$step][$data])) {
            return false;
        }
        return $steps[$step][$data];
    }
    
    public function clear()
    {
        Mage::dispatchEvent('destoryQuote', array('quote'=>$this->getQuote()));
        $this->_quote = null;
        $this->setQuoteId(null);
    }
    
    public function resetCheckout()
    {
        $this->setCheckoutState(self::CHECKOUT_STATE_BEGIN);
        return $this;
    }
}
