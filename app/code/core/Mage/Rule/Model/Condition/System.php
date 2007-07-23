<?php

class Mage_Rule_Model_Condition_System extends Mage_Rule_Model_Condition_Abstract
{
    public function loadAttributes()
    {
        $this->setAttributeOption(array(
            'date'=>'Date',
            'visitor_ip'=>'Visitor IP Address',
            'store_code'=>'Store code',
            'language_code'=>'Language code',
        ));
        return $this;
    }
    
    public function asString($format='')
    {
        return 'System '.parent::asString();
    }
    
    public function validate()
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