<?php

class Mage_Sales_Model_Quote_Rule_Condition_Quote_Item extends Mage_Rule_Model_Condition_Abstract
{
    public function loadAttributeOptions()
    {
        $this->setAttributeOption(array(
            'product_id'=>'Product ID',
            'sku'=>'SKU',
            'qty'=>'Quantity',
            'brand'=>'Brand',
            'weight'=>'Weight',
        ));
        return $this;
    }
}