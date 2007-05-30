<?php

class Mage_Sales_Model_Quote_Rule_Condition_Customer extends Mage_Sales_Model_Quote_Rule_Condition_Abstract
{
    public function loadAttributes()
    {
        $this->setAttributeOption(array(
            'type'=>'Type',
            'registered'=>'Registered',
            'first_time_buyer'=>'First time buyer',
        ));
        return $this;
    }
    
    public function toString($format='')
    {
        return 'Customer '.parent::toString();
    }
}