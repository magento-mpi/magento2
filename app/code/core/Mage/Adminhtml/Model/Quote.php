<?php

class Mage_Adminhtml_Model_Quote extends Mage_Core_Model_Session_Abstract
{

    /**
     * Admin order creation quote
     *
     * @var Mage_Sales_Model_Quote
     */
    protected $_quote = null;

    /**
     * Enter description here...
     *
     * @var Mage_Customer_Model_Customer
     */
    protected $_customer = null;

    /**
     * Enter description here...
     *
     * @var Mage_Directory_Model_Currency
     */
    protected $_currency = null;

    public function __construct()
    {
        $this->init('quote');
    }

    /**
     * Enter description here...
     *
     * @return Mage_Adminhtml_Model_Quote
     */
    public function unsetAll()
    {
        parent::unsetAll();
        $this->_quote = null;
        return $this;
    }

    /**
     * Enter description here...
     *
     * @return Mage_Sales_Model_Quote
     */
    public function getQuote()
    {
        if (empty($this->_quote)) {
            $quote = Mage::getModel('sales/quote');
            /* @var $quote Mage_Sales_Model_Quote */
            if ($this->getQuoteId()) {
                $quote->load($this->getQuoteId());
                if (!$quote->getId()) {
                    $quote->setQuoteId(null);
                }
            }
            if (!$this->getQuoteId()) {
                $quote->initNewQuote()
                    ->setStoreId($this->getStoreId())
                    ->setCustomerId($this->getCustomerId())
                    ->save();
                $this->setQuoteId($quote->getId());
            }
            $this->_quote = $quote;
        }
        return $this->_quote;
    }

    /**
     * Enter description here...
     *
     * @return Mage_Adminhtml_Model_Quote
     */
    public function reset()
    {
        $this->unsetAll();
        return $this;
    }

    /**
     * Enter description here...
     *
     * @return Mage_Adminhtml_Model_Quote
     */
    public function setCustomerId($customerId)
    {
        if ($oldCustomerId = $this->getCustomerId()) {
            if ($oldCustomerId != $customerId) {
                $this->reset();
            }
        }
        $this->setData('customer_id', $customerId);
        if (intval($customerId)) {
            $this->setCustomer(Mage::getModel('customer/customer')->load($customerId));
        }
        return $this;
    }

    /**
     * Enter description here...
     *
     * @return Mage_Adminhtml_Model_Quote
     */
    public function setCustomer($customer)
    {
        $this->_customer = $customer;
        return $this;
    }

    /**
     * Enter description here...
     *
     * @return Mage_Customer_Model_Customer
     */
    public function getCustomer()
    {
        if (is_null($this->_customer)) {
            $customer = Mage::getModel('customer/customer');
            if (($customerId = $this->getCustomerId()) && intval($customerId)) {
                $customer->load($customerId);
            }
            $this->setCustomer($customer);
        }
        return $this->_customer;
    }

    /**
     * Enter description here...
     *
     * @return string
     */
    public function getCustomerName()
    {
        if ($this->getIsOldCustomer()) {
            return $this->getCustomer()->getName();
        } elseif ('new' === $this->getCustomerId()) {
            return __('new customer');
        }
        return '';
    }

    /**
     * Enter description here...
     *
     * @param int $storeId
     * @return Mage_Adminhtml_Model_Quote
     */
    public function setStoreId($storeId)
    {
        $this->setData('store_id', $storeId);

        if (! in_array($storeId, $this->getCustomer()->getSharedStoreIds())) {
            echo '-------------------' . "\n";
            echo 'clonig customer to new store' . "\n";
            echo '-------------------' . "\n";
            $customer = clone $this->getCustomer();
            $customer->setStoreId($storeId);
            $customer->save();
        }
        return $this;
    }

    /**
     * Get customer's front-end quote
     *
     * @param bool $create create quote if still not exists
     * @return Mage_Sales_Model_Quote|false
     */
    public function getCustomerQuote($create = true)
    {
        if ($this->getIsOldCustomer()) {
            $quote = Mage::getModel('sales/quote');
            /* @var $quote Mage_Sales_Model_Quote */
            $loadedQuote = $quote->getResourceCollection()->loadByCustomerId($this->getCustomerId());
            if ($loadedQuote) {
                return $loadedQuote;
            }
            $quote->initNewQuote()
                ->setStoreId($this->getStoreId())
                ->setCustomerId($this->getCustomerId())
                ->save();
            return $quote;
        }
        return false;
    }

    /**
     * Enter description here...
     *
     * @return boolean
     */
    public function getIsOldCustomer()
    {
        if (intval($this->getCustomerId())) {
            return true;
        }
        return false;
    }

    /**
     * Enter description here...
     *
     * @return Mage_Directory_Model_Currency
     */
    public function getCurrency()
    {
        if (is_null($this->_currency) && $this->getStoreId()) {
            $this->setCurrency(Mage::getModel('directory/currency')->load($this->getQuote()->getStore()->getConfig('general/currency/default')));
        }
        return $this->_currency;
    }

    /**
     * Enter description here...
     *
     * @param Mage_Directory_Model_Currency $currency
     * @return Mage_Adminhtml_Model_Quote
     */
    public function setCurrency($currency)
    {
        $this->_currency = $currency;
        return $this;
    }

    /**
     * Enter description here...
     *
     * @param float $price
     * @return string
     */
    public function formatPrice($price)
    {
        return $this->getCurrency()->format($price);
    }

}
