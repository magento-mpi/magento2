<?php

class Mage_Sales_Model_Quote_Rule_Condition_System extends Mage_Sales_Model_Quote_Rule_Condition_Abstract
{
    public function loadAttributes()
    {
        $this->setAttributeOption(array(
            'date'=>'Date',
        ));
        return $this;
    }
    
    public function toString($format='')
    {
        return 'System '.parent::toString();
    }
}