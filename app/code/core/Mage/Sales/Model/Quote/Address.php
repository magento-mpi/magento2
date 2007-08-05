<?php

class Mage_Sales_Model_Quote_Address extends Mage_Core_Model_Abstract
{
    protected $_quote;
    
    protected $_rates;
    
    protected $_totals = array();
    
    protected function _construct()
    {
        $this->_init('sales/quote_address');
    }
    
    public function setQuote(Mage_Sales_Model_Quote $quote)
    {
        $this->_quote = $quote;
        return $this;
    }
    
    public function getQuote()
    {
        return $this->_quote;
    }
    
/*********************** ADDRESS ***************************/

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
    
    public function toArray(array $arrAttributes = array())
    {
        $arr = parent::toArray();
        $arr['rates'] = $this->getShippingRatesCollection()->toArray($arrAttributes);
        foreach ($this->getTotals() as $k=>$total) {
            $arr['totals'][$k] = $total->toArray();
        }
        return $arr;
    }
    
/*********************** ITEMS ***************************/

    public function getItemsCollection()
    {
        if (empty($this->_items)) {
            $this->_items = Mage::getResourceModel('sales/quote_address_item_collection');
            
            if ($this->getId()) {
                $this->_items
                    ->addAttributeToSelect('*')
                    ->setAddressFilter($this->getId())
                    ->load();
                foreach ($this->_items as $item) {
                    $item->setAddress($this);
                }
            }
        }
        return $this->_items;
    }
    
    public function getAllItems()
    {
        $items = array();
        if ($this->getQuote()->getIsMultiShipping()) {
            $itemsCollection = $this->getItemsCollection();
        } else {
            $itemsCollection = $this->getQuote()->getItemsCollection();
        }
        foreach ($itemsCollection as $item) {
            if (!$item->isDeleted()) {
                $items[] =  $item;
            }
        }
        return $items;
    }
    
    
    public function hasItems()
    {
        return sizeof($this->getAllItems())>0;
    }
    
    public function getItemById($itemId)
    {
        foreach ($this->getItemsCollection() as $item) {
            if ($item->getId()==$itemId) {
                return $item;
            }
        }
        return false;
    }
    
    public function removeItem($itemId)
    {
        foreach ($this->getItemsCollection() as $item) {
            if ($item->getId()==$itemId) {
                $item->isDeleted(true);
                break;
            }
        }
        return $this;
    }
    
    public function addItem(Mage_Sales_Model_Quote_Address_Item $item)
    {
        $item->setAddress($this)->setParentId($this->getId());
        if (!$item->getId()) {
            $this->getItemsCollection()->addItem($item);
        }
        return $this;
    }


/*********************** SHIPPING RATES ***************************/

    public function getShippingRatesCollection()
    {
        if (empty($this->_rates)) {
            $this->_rates = Mage::getResourceModel('sales/quote_address_rate_collection');
            if ($this->getId()) {
                $this->_rates
                    ->addAttributeToSelect('*')
                    ->setAddressFilter($this->getId())
                    ->load();
                foreach ($this->_rates as $rate) {
                    $rate->setAddress($this);
                }
            }
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
    
/*********************** TOTALS ***************************/

    public function collectTotals()
    {
        $this->getResource()->collectTotals($this);
        return $this;
    }
    
    public function getTotals()
    {
        if (empty($this->_totals)) {
            $this->getResource()->fetchTotals($this);
        }
        return $this->_totals;
    }
    
    public function addTotal($total)
    {
        if (is_array($total)) {
            $totalInstance = Mage::getModel('sales/quote_address_total')
                ->setData($total);
        } elseif ($total instanceof Mage_Sales_Model_Quote_Total) {
            $totalInstance = $total;
        }
        $this->_totals[$totalInstance->getCode()] = $totalInstance;
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