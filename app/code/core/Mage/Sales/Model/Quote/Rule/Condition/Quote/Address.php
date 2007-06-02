<?php

class Mage_Sales_Model_Quote_Rule_Condition_Quote_Address extends Mage_Sales_Model_Quote_Rule_Condition_Abstract
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
    
    public function validateQuoteAddress(Mage_Sales_Model_Quote_Entity_Address $address)
    {
        return $this->validateAttribute($address->getData($this->getAttribute()));
    }
}