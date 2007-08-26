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
            	$itemProduct = $products->getItemById($item->getProductId());
            	if ($itemProduct->isVisibleInCatalog()) {
                	if ($itemProduct) {
                	    $item->setStatus($itemProduct->getStatus());
        	        	$item->setPrice($itemProduct->getFinalPrice($item->getQty()));
        	        	$item->setName($itemProduct->getName());
        	        	$item->setTaxClassId($itemProduct->getTaxClassId());
        	        	$item->setWeight($itemProduct->getWeight());
        	        	$item->setProduct($itemProduct);
                	}
                    $item->calcRowTotal();
                    $address->setSubtotal($address->getSubtotal() + $item->getRowTotal());
            	}
            	else {
            	    $address->removeItem($item->getId());
            	    if ($address->getQuote()) {
            	        $address->getQuote()->removeItem($item->getQuoteItemId());
            	    }
            	}
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