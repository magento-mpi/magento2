<?php

class Mage_Sales_Model_Quote_Rule_Condition_Quote_Address_Combine extends Mage_Sales_Model_Quote_Rule_Condition_Combine
{
    public function setRule(Mage_Sales_Model_Quote_Rule $rule)
    {
        $this->setData('rule', $rule);
        $number = $rule->getConditionAddressNumber();
        $rule->setConditionAddressNumber($number+1);
        $this->setAddressNumber($number);
        return $this;
    }
    
    public function addCondition(Mage_Sales_Model_Quote_Rule_Condition_Abstract $condition)
    {
        $condition->setType('quote_address');
        return parent::addCondition($condition);
    }
    
    public function toString($format='')
    {
        $str = parent::toString()." for same address (# ".$this->getAddressNumber()."):";
        return $str;
    }
}