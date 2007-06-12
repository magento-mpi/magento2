<?php

class Mage_Catalog_Model_Product_Rule_Action_Stop extends Mage_Core_Model_Rule_Action_Stop
{
    public function updateProduct(Mage_Catalog_Model_Product $product)
    {
        $this->getRule()->setStopProcessingRules(true);
        return $this;
    }
}