<?php

class Mage_CatalogRule_Model_Rule_Condition_Product extends Mage_Rule_Model_Condition_Abstract
{
    public function loadAttributeOptions()
    {
        $this->setAttributeOption(array(
            'product_id'=>'Product ID',
            'sku'=>'SKU',
            'brand'=>'Brand',
            'weight'=>'Weight',
        ));
        return $this;
    }
}