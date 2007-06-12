<?php

class Mage_Sales_Model_Quote_Rule_Condition_Combine extends Mage_Core_Model_Rule_Condition_Combine
{
    public function validateQuote(Mage_Sales_Model_Quote $quote)
    {
        $all = $this->getAttribute()==='all';
        $true = (bool)$this->getValue();
        foreach ($this->getConditions() as $cond) {
            if ($all && $cond->validateQuote($quote)!==$true) {
                return false;
            } elseif (!$all && $cond->validateQuote($quote)===$true) {
                return true;
            }
        }
        return $all ? true : false;
    }
}