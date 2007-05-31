<?php

class Mage_Sales_Model_Quote_Rule_Condition_Quote_Item_Combine extends Mage_Sales_Model_Quote_Rule_Condition_Combine
{
    public function __construct()
    {
        parent::__construct();
        $this->setType('quote_item_combine');
    }
    
    public function setRule(Mage_Sales_Model_Quote_Rule $rule)
    {
        $this->setData('rule', $rule);
        $number = $rule->getConditionItemNumber();
        $rule->setConditionItemNumber($number+1);
        $this->setItemNumber($number);
        return $this;
    }
    
    public function addCondition(Mage_Sales_Model_Quote_Rule_Condition_Abstract $condition)
    {
        $condition->setType('quote_item');
        return parent::addCondition($condition);
    }
    
    public function toString($format='')
    {
        $str = parent::toString()." for same item (# ".$this->getItemNumber().")";
        return $str;
    }
    
    public function validateQuote(Mage_Sales_Model_Quote $quote)
    {
        return true;
    }
}