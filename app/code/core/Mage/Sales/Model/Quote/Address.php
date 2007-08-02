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
    
    public function addShippingRate(Mage_Sales_Shipping_Rate_Result_Abstract $rate)
    {
        $addressRate = Mage::getModel('sales/quote_address_rate');
        
        if ($rate instanceof Mage_Sales_Model_Shipping_Rate_Result_Error) {
            $addressRate->setCarrier($rate->getCarrier());
            $addressRate->setErrorMessage($rate->getErrorMessage());
        } else {
            $addressRate->setParentId($this->getId());
            $addressRate->setCode($rate->getCarrier().'_'.$rate->getMethod());
            $addressRate->setCarrier($rate->getCarrier());
            $addressRate->setMethod($rate->getMethod());
            $addressRate->setMethodDescription($rate->getMethodTitle());
            $addressRate->setPrice($rate->getPrice());
        }
    
        return $addressRate;
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
        $rates = $result->getAllRates();
        
        foreach ($rates as $rate) {
            $this->addShippingRate($rate);
            
            if ($this->getShippingMethod()==$addressRate->getCode()) {
                $this->setShippingAmount($addressRate->getPrice());
            }
        }
        
        return $this;
    }
    
/*********************** ORDERS ***************************/

    public function createOrder()
    {
        $store = Mage::getSingleton('core/store');
        $now = now();
        
        $quote = $this->getQuote();
        $order = Mage::getModel('sales/order');
        
        $order->setRealOrderId(Mage::getResourceModel('sales/counter')->getCounter('order'))
            ->setCustomerId()       
            ->setRemoteIp(Mage::registry('controller')->getRequest()->getServer('REMOTE_ADDR'))
            ->setQuoteId($quote->getId())
            ->setCurrencyId($store->getCurrencyId())
            ->setCurrencyBaseId($store->getCurrencyBaseId())
            ->setCurrencyRate($store->getCurrencyRate());
        
        foreach (array('item', 'address', 'payment') as $entityType) {
            $entities = $this->getEntitiesByType($entityType);
            foreach ($entities as $quoteEntity) {
                $entity = Mage::getModel('sales/order_entity_'.$entityType)->addData($quoteEntity->getData());
                $order->addEntity($entity);
            }
        }
        
        $status = $this->getPayment()->getOrderStatus();
        $order->setStatus($status);
        $statusEntity = Mage::getModel('sales/order_entity_status')
            ->setStatus($status)
            ->setCreatedAt($now);
            
        $order->validate();
        if ($order->getErrors()) {
            //TODO: handle errors (exception?)
        }
        
        $order->save();
        
        $this->setConvertedAt($now)->setCreatedOrderId($order->getId())->save();
        $this->setLastCreatedOrder($order);
        
        return $this;
    }    
}