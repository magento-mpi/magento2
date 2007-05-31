<?php

class Mage_Sales_Model_Quote_Rule_Action_Quote_Address extends Mage_Sales_Model_Quote_Rule_Action_Abstract
{
    public function loadAttributes()
    {
        $this->setAttributeOption(array(
            'postcode'=>'Zip code',
            'region_id'=>'Region/State',
            'country_id'=>'Country',
        ));
        return $this;
    }
    
    public function toString($format='')
    {
        $str = "Update address # ".$this->getAddressNumber()." ".$this->getAttributeName()
            ." ".$this->getOperatorName()." ".$this->getValueName();
        return $str;
    }
    
    public function updateQuote(Mage_Sales_Model_Quote $quote)
    {
        return $this;
    }
}