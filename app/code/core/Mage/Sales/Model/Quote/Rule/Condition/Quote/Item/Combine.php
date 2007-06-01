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
        $str = "If an item is ".($this->getValue() ? 'FOUND' : 'NOT FOUND')
            .' in the cart with '.$this->getAttributeName()." of these conditions (# ".$this->getItemNumber().")";
        return $str;
    }
    
    public function validateQuote(Mage_Sales_Model_Quote $quote)
    {
        $all = $this->getAttribute()==='all';
        $found = false;
        foreach ($quote->getEntitiesByType('item') as $item) {
            $found = $all ? true : false;
            foreach ($this->getConditions() as $cond) {
                if ($all && !$cond->validateQuoteItem($item)) {
                    $found = false;
                    break;
                } elseif (!$all && $cond->validateQuoteItem($item)) {
                    $found = true;
                    break 2;
                }
            }
        }
        if ($found && $this->getValue()) { 
            // found an item and we're looking for existing one
            
            return true;
        } elseif (!$found && !$this->getValue()) {
            // not found and we're making sure it doesn't exist
            return true;
        }
        return false;
    }
}