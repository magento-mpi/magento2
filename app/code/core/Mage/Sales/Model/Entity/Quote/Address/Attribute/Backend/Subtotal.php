<?php

class Mage_Sales_Model_Entity_Quote_Address_Attribute_Backend_Subtotal
    extends Mage_Sales_Model_Entity_Quote_Address_Attribute_Backend
{
    public function collectTotals(Mage_Sales_Model_Quote_Address $address)
    {
    	
    	$productIds = array();
        foreach ($address->getAllItems() as $item) {
			$productIds[$item->getProductId()] = $item->getProductId();
        }
        $products = Mage::getResourceModel('catalog/product_collection');
        $products->getEntity()->setStore($address->getStoreId());
       	$products->addAttributeToFilter('entity_id', array('in'=>$productIds));
       	$products->addAttributeToSelect('*');
       	$products->load();    
       	
        $address->setSubtotal(0);

        foreach ($address->getAllItems() as $item) {
        	
        	$p = $products->getItemById($item->getProductId());
        	$item->setPrice($p->getFinalPrice());
        	$item->setName($p->getName());
        	$item->setTaxClassId($p->getTaxClassId());
        	$item->setWeight($p->getWeight());
        	
            $item->calcRowTotal();
            $address->setSubtotal($address->getSubtotal() + $item->getRowTotal());
        }
       
        $address->setGrandTotal($address->getSubtotal());
            
        return $this;
    }

}