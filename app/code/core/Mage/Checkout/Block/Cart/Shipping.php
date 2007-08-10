<?php

class Mage_Checkout_Block_Cart_Shipping extends Mage_Checkout_Block_Cart_Abstract 
{
    public function getQuote()
    {
        if (empty($this->_quote)) {
            $this->_quote = Mage::getSingleton('checkout/session')->getQuote();
        }
        return $this->_quote;
    }

    public function getEstimateRates()
    {
        if (empty($this->_rates)) {
            $groups = $this->getAddress()->getGroupedAllShippingRates();
            if (!empty($groups)) {
                $ratesFilter = new Varien_Filter_Object_Grid();
                $ratesFilter->addFilter(new Varien_Filter_Sprintf('$%s', 2), 'price');
                
                foreach ($groups as $code => $groupItems) {
                	$groups[$code] = $ratesFilter->filter($groupItems);
                }
            }
            return $this->_rates = $groups;
        }
        return $this->_rates;
    }
    
    public function getAddress()
    {
        if (empty($this->_address)) {
            $this->_address = $this->getQuote()->getShippingAddress();
        }
        return $this->_address;
    }
    
    public function getCarrierName($carrierCode)
    {
        if ($name = Mage::getStoreConfig('carriers/'.$carrierCode.'/title')) {
            return $name;
        }
        return $carrierCode;
    }
    
    public function getAddressShippingMethod()
    {
        return $this->getAddress()->getShippingMethod();
    }
    
    public function getEstimatePostcode()
    {
        return $this->getQuote()->getShippingAddress()->getPostcode();
    }        
}