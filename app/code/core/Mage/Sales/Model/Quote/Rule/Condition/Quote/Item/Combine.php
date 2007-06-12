<?php

class Mage_Sales_Model_Quote_Rule_Condition_Quote_Item_Combine extends Mage_Rule_Model_Condition_Combine
{
    public function __construct()
    {
        parent::__construct();
        $this->setType('quote_item_combine');
    }
    
    public function setRule(Mage_Sales_Model_Quote_Rule $rule)
    {
        $this->setData('rule', $rule);
        $number = $rule->getFoundQuoteItemNumber();
        $rule->setFoundQuoteItemNumber($number+1);
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
            .' in the cart with '.$this->getAttributeName()." of these conditions true (# ".$this->getItemNumber().")";
        return $str;
    }
    
    public function validate()
    {
        $all = $this->getAttribute()==='all';
        $found = false;
        foreach ($quote->getEntitiesByType('item') as $item) {
            $found = $all ? true : false;
            foreach ($this->getConditions() as $cond) {
                $cond->setObject($item);
                if ($all && !$cond->validate()) {
                    $found = false;
                    break;
                } elseif (!$all && $cond->validate()) {
                    $found = true;
                    break 2;
                }
            }
        }
        if ($found && $this->getValue()) { 
            // found an item and we're looking for existing one
            
            $foundItems = $this->getRule()->getFoundQuoteItems();
            $foundItems[$this->getItemNumber()] = $item->getEntityId();
            $this->getRule()->setFoundQuoteItems($foundItems);
            
            return true;
        } elseif (!$found && !$this->getValue()) {
            // not found and we're making sure it doesn't exist
            return true;
        }
        return false;
    }
}