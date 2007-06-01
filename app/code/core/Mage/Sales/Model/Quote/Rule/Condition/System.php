<?php

class Mage_Sales_Model_Quote_Rule_Condition_System extends Mage_Sales_Model_Quote_Rule_Condition_Abstract
{
    public function loadAttributes()
    {
        $this->setAttributeOption(array(
            'date'=>'Date',
            'visitor_ip'=>'Visitor IP Address'
        ));
        return $this;
    }
    
    public function toString($format='')
    {
        return 'System '.parent::toString();
    }
    
    public function validateQuote(Mage_Sales_Model_Quote $quote)
    {
        switch ($this->getAttribute()) {
            case 'date':
                if (!is_numeric($this->getValue())) {
                    $this->setValue(strtotime($this->getValue()));
                }
                return $this->validateAttribute(time());
                
            case 'visitor_ip':
                if (!is_numeric($this->getValue())) {
                    $this->setValue(ip2long($this->getValue()));
                }
                return $this->validateAttribute(ip2long($_SERVER['REMOTE_ADDR']));
        }
        return false;
    }
}