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
        $arr['items'] = $this->getItemsCollection()->toArray($arrAttributes);
        foreach ($this->getTotals() as $k=>$total) {
            $arr['totals'][$k] = $total->toArray();
        }
        return $arr;
    }

/*********************** STREET LINES ***************************/

    /**
     * get address street
     *
     * @param   int $line address line index
     * @return  string
     */
    public function getStreet($line=0)
    {
        $street = parent::getData('street');
        if (-1===$line) {
            return $street;
        } else {
            $arr = is_array($street) ? $street : explode("\n", $street);
            if (0===$line) {
                return $arr;
            } elseif (isset($arr[$line-1])) {
                return $arr[$line-1];
            } else {
                return '';
            }
        }
    }
    
    /**
     * set address street informa
     *
     * @param unknown_type $street
     * @return unknown
     */
    public function setStreet($street)
    {
        if (is_array($street)) {
            $street = trim(implode("\n", $street));
        }
        $this->setData('street', $street);
        return $this;
    }
    
    /**
     * To be used when processing _POST
     */
    public function implodeStreetAddress()
    {
        $this->setStreet($this->getData('street'));
    }
    
    /**
     * set address data
     *
     * @param   string $key
     * @param   mixed $value
     * @return  Mage_Sales_Model_Quote_Address
     */
    public function setData($key, $value='')
    {
        switch ($key) {
            case 'region':
                if (is_numeric($value)) {
                    $region = Mage::getModel('directory/region')->load((int)$value);
                    if ($region->getId()) {
                        $this->setRegionId($value);
                        $this->setRegion($region->getCode());
                        return $this;
                    }
                }
                break;
        }
        return parent::setData($key, $value);
    }

    
/*********************** ITEMS ***************************/

    public function getItemsCollection()
    {
        if (is_null($this->_items)) {
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
        $quoteItems = $this->getQuote()->getItemsCollection();
        $addressItems = $this->getItemsCollection();

        $items = array();
        if ($this->getQuote()->getIsMultiShipping() && !$addressItems->count()==0) {
            foreach ($addressItems as $aItem) {
                if ($aItem->isDeleted()) {
                    continue;
                }
                if (!$aItem->getIsCompletedFromQuoteItem()) {
                    foreach ($quoteItems as $qItem) {
                        if ($aItem->getQuoteItemId()==$qItem->getId()) {
                            foreach ($qItem->getData() as $k=>$v) {
                                if (!$aItem->hasData($k)) {
                                    $aItem->setData($k, $v);
                                }
                            }
                            $aItem->setIsCompletedFromQuoteItem(true);
                            break;
                        }
                    }
                }
                $items[] = $aItem;
            }
        } else {
            foreach ($quoteItems as $qItem) {
                if ($qItem->isDeleted()) {
                    continue;
                }
                $items[] = $qItem;
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
    
    public function getItemByQuoteItemId($itemId)
    {
        foreach ($this->getItemsCollection() as $item) {
            if ($item->getQuoteItemId()==$itemId) {
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
        $item->setAddress($this)
            ->setParentId($this->getId());
        if (!$item->getId()) {
            $this->getItemsCollection()->addItem($item);
        }
        return $this;
    }


/*********************** SHIPPING RATES ***************************/

    public function getShippingRatesCollection()
    {
        if (is_null($this->_rates)) {
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
        
        $request = Mage::getModel('shipping/rate_request');
        $request->setDestCountryId($this->getCountryId());
        $request->setDestRegionId($this->getRegionId());
        $request->setDestPostcode($this->getPostcode());
        $request->setPackageValue($this->getSubtotal());
        $request->setPackageWeight($this->getWeight());
        
        $result = Mage::getModel('shipping/shipping')
            ->collectRates($request)->getResult();
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
        if (is_null($this->_totals)) {
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
        
        $this->getQuote()
            ->setConvertedAt(now())
            ->setLastCreatedOrder($order)
            ->save();
        
        return $order;
    }
}