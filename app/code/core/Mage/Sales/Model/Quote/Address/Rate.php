<?php

class Mage_Sales_Model_Quote_Address_Rate extends Mage_Core_Model_Abstract
{
    protected $_address;
    
    protected function _construct()
    {
        $this->_init('sales/quote_address_rate');
    }
    
    public function setAddress(Mage_Core_Model_Quote_Address $address)
    {
        $this->_address = $address;
        return $this;
    }
    
    public function getAddress()
    {
        return $this->_address;
    }
    
    public function importShippingRate(Mage_Sales_Model_Shipping_Rate_Result_Abstract $rate)
    {
        if ($rate instanceof Mage_Sales_Model_Shipping_Rate_Result_Error) {
            $this
                ->setCarrier($rate->getCarrier())
                ->setErrorMessage($rate->getErrorMessage())
            ;
        } else {
            $this
                ->setParentId($this->getId())
                ->setCode($rate->getCarrier().'_'.$rate->getMethod())
                ->setCarrier($rate->getCarrier())
                ->setMethod($rate->getMethod())
                ->setMethodDescription($rate->getMethodTitle())
                ->setPrice($rate->getPrice())
            ;
        }
        return $this;
    }
}