<?php

class Mage_Sales_Model_Quote_Rule_Condition_Quote_Item extends Mage_Sales_Model_Quote_Rule_Condition_Abstract
{
    public function loadAttributes()
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
    
    public function validateQuoteItem(Mage_Sales_Model_Quote_Entity_Item $item)
    {
        return $this->validateAttribute($item->getData($this->getAttribute()));
    }
}