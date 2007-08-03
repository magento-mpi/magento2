<?php

class Mage_Sales_Model_Quote_Address extends Mage_Core_Model_Abstract
{
    protected $_quote;
    
    protected $_rates;
    
    protected function _construct()
    {
        $this->_init('sales/quote_address');
    }
    
    public function setQuote(Mage_Core_Model_Quote $quote)
    {
        $this->_quote = $quote;
        return $this;
    }
    
    public function getQuote()
    {
        return $this->_quote;
    }
    
    public function collectTotals()
    {
        $this->getResource()->collectTotals($this);
        return $this;
    }
    
    public function getTotals()
    {
        return $this->getResource()->getTotals($this);
    }
    
    public function importCustomerAddress(Mage_Customer_Model_Address $address)
    {
        $this
            ->setCustomerAddressId($address->getId())
            ->setCustomerId($address->getParentId())
            ->setEmail($address->getCustomer()->getEmail())
            ->setFirstname($address->getFirstname())
            ->setLastname($address->getLastname())
            ->setCompany($address->getCompany())
            ->setStreet($address->getStreet())
            ->setCity($address->getCity())
            ->setRegion($address->getRegion())
            ->setRegionId($address->getRegionId())
            ->setPostcode($address->getPostcode())
            ->setCountryId($address->getCountryId())
            ->setTelephone($address->getTelephone())
            ->setFax($address->getFax())
        ;
        return $this;
    }
    
/*********************** SHIPPING RATES ***************************/

    public function getShippingRatesCollection()
    {
        if (empty($this->_rates)) {
            $this->_rates = Mage::getModel('sales_entity/quote_address_rate_collection')
                ->setAddressFilter($this->getId())
                ->load();
        }
        return $this->_rates;
    }
    
    public function getAllShippingRates()
    {
        $rates = array();
        foreach ($this->getShippingRatesCollection() as $rate) {
            if (!$rate->isDeleted()) {
                $rates[] = $rate;
            }
        }
        return $rates;
    }
    
    public function getShippingRateById($rateId)
    {
        foreach ($this->getShippingRatesCollection() as $rate) {
            if ($rate->getId()==$rateId) {
                return $rate;
            }
        }
        return false;
    }
    
    public function removeAllShippingRates()
    {
        foreach ($this->getShippingRatesCollection() as $rate) {
            $rate->isDeleted(true);
        }
        return $this;
    }
    
    public function addShippingRate(Mage_Sales_Model_Quote_Address_Rate $rate)
    {
        $rate->setQuote($this)->setParentId($this->getId());
        $this->getShippingRatesCollection()->addItem($rate);
        return $this;
    }

    public function collectShippingRates()
    {
        $this->removeAllShippingRates();
        
        $request = Mage::getModel('sales/shipping_rate_request');
        $request->setDestCountryId($this->getCountryId());
        $request->setDestRegionId($this->getRegionId());
        $request->setDestPostcode($this->getPostcode());
        $request->setPackageValue($this->getSubtotal());
        $request->setPackageWeight($this->getWeight());
        
        $result = Mage::getModel('sales/shipping')->collectRates($request);
        if (!$result) {
            return $this;
        }
        $shippingRates = $result->getAllRates();
        
        foreach ($shippingRates as $shippingRate) {
            $rate = Mage::getModel('sales/quote_address_rate')
                ->importShippingRate($shippingRate); 
            $this->addShippingRate($rate);
            
            if ($this->getShippingMethod()==$rate->getCode()) {
                $this->setShippingAmount($rate->getPrice());
            }
        }
        
        return $this;
    }
    
/*********************** ORDERS ***************************/

    public function createOrder()
    {
        $order = Mage::getModel('sales/order')
            ->createFromQuoteAddress($this);
        
        $order->save();
        
        $quote
            ->setConvertedAt($now)
            ->setLastCreatedOrder($order);
        $quote->save();
        
        return $order;
    }    
}