<?php

class Mage_Catalog_Model_Product_Rule_Condition_Product extends Mage_Core_Model_Rule_Condition_Abstract
{
    public function loadAttributes()
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