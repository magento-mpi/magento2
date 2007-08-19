<?php

class Mage_SalesRule_Model_Rule_Condition_Product extends Mage_Rule_Model_Condition_Abstract
{
    public function loadAttributeOptions()
    {
        $productAttributes = Mage::getResourceSingleton('catalog/product')
            ->loadAllAttributes()->getAttributesByCode();
            
        $attributes = array();
        foreach ($productAttributes as $attr) {
            if (!$attr->getIsVisible()) {
                continue;
            }
            $attributes[$attr->getAttributeCode()] = $attr->getFrontend()->getLabel();
        }

        asort($attributes);
        $this->setAttributeOption($attributes);
        
        return $this;
    }
    
    public function collectValidatedAttributes($productCollection)
    {
        $productCollection->addAttributeToSelect($this->getAttribute());
        return $this;
    }
    
    public function validate(Varien_Object $object)
    {
    	$product = Mage::getModel('catalog/product')
    		->load($object->getProductId())
    		->setQty($object->getQty())
    		->setPrice($object->getPrice())
    		->setRowTotal($object->getRowTotal());
    		
    	return parent::validate($product);
    }
}