<?php

class Mage_Sales_Model_Quote_Rule_Condition_Quote_Address_Combine extends Mage_Rule_Model_Condition_Combine
{
    public function __construct()
    {
        parent::__construct();
        $this->setType('quote_address_combine');
    }
    
    public function setRule(Mage_Sales_Model_Quote_Rule $rule)
    {
        $this->setData('rule', $rule);
        $number = $rule->getFoundQuoteAddressNumber();
        $rule->setFoundQuoteAddressNumber($number+1);
        $this->setAddressNumber($number);
        return $this;
    }
    
    public function addCondition(Mage_Sales_Model_Quote_Rule_Condition_Abstract $condition)
    {
        $condition->setType('quote_address');
        return parent::addCondition($condition);
    }
    
    public function asString($format='')
    {
        $str = "If ".$this->getAttributeName()." of these conditions are ".$this->getValueName()." for shipping address (# ".$this->getAddressNumber().")";
        return $str;
    }
    
    public function validate()
    {
        $all = $this->getAttribute()==='all';
        $found = false;
        foreach ($this->getQuote()->getShippingAddresses() as $address) {
            $found = $all ? true : false;
            foreach ($this->getConditions() as $cond) {
                $cond->setObject($address);
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
            
            $foundAddresses = $this->getRule()->getFoundQuoteAddreses();
            $foundAddresses[$this->getAddressNumber()] = $item->getEntityId();
            $this->getRule()->setFoundQuoteAddresses($foundAddresses);
            
            return true;
        } elseif (!$found && !$this->getValue()) {
            // not found and we're making sure it doesn't exist
            return true;
        }
        return false;
    }
}