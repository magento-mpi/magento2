<?php

class Mage_Catalog_Model_Product_Rule_Condition_Combine extends Mage_Core_Model_Rule_Condition_Combine
{
    public function validateProduct(Mage_Catalog_Model_Product $product)
    {
        $all = $this->getAttribute()==='all';
        $true = (bool)$this->getValue();
        foreach ($this->getConditions() as $cond) {
            if ($all && $cond->validateQuote($product)!==$true) {
                return false;
            } elseif (!$all && $cond->validateQuote($product)===$true) {
                return true;
            }
        }
        return $all ? true : false;
    }
}