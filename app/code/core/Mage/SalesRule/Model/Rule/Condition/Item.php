<?php

class Mage_SalesRule_Model_Rule_Condition_Item extends Mage_Rule_Model_Condition_Abstract
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