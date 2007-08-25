<?php

class Mage_Sales_Model_Quote_Address_Total_Subtotal
    extends Mage_Sales_Model_Quote_Address_Total_Abstract
{
    public function collect(Mage_Sales_Model_Quote_Address $address)
    {

        $address->setSubtotal(0);

        if (count($address->getAllItems())) {
        	$productIds = array();
            foreach ($address->getAllItems() as $item) {
    			$productIds[$item->getProductId()] = $item->getProductId();
            }
            $products = Mage::getResourceModel('catalog/product_collection');
            $products->getEntity()->setStore($address->getStoreId());
           	$products->addAttributeToFilter('entity_id', array('in'=>$productIds));
           	$products->addAttributeToSelect('*');
           	$products->load();

            foreach ($address->getAllItems() as $item) {

            	$p = $products->getItemById($item->getProductId());
            	if ($p) {
    	        	$item->setPrice($p->getFinalPrice());
    	        	$item->setName($p->getName());
    	        	$item->setTaxClassId($p->getTaxClassId());
    	        	$item->setWeight($p->getWeight());
            	}

                $item->calcRowTotal();
                $address->setSubtotal($address->getSubtotal() + $item->getRowTotal());
            }
        }

        $address->setGrandTotal($address->getSubtotal());

        return $this;
    }
    
    public function fetch(Mage_Sales_Model_Quote_Address $address)
    {
        $address->addTotal(array(
            'code'=>$this->getCode(), 
            'title'=>__('Subtotal'), 
            'value'=>$address->getSubtotal()
        ));

        return $this;
    }
}