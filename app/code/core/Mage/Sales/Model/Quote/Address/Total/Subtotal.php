<?php

class Mage_Sales_Model_Quote_Address_Total_Subtotal
    extends Mage_Sales_Model_Quote_Address_Total_Abstract
{
    /**
     * Collect address subtotal
     *
     * @param   Mage_Sales_Model_Quote_Address $address
     * @return  Mage_Sales_Model_Quote_Address_Total_Subtotal
     */
    public function collect(Mage_Sales_Model_Quote_Address $address)
    {
        $address->setSubtotal(0);
        
        $items = $address->getAllItems();
        if (count($items)) {
            $products = $this->_getItemsProductCollection($items, $address->getStoreId());
            foreach ($items as $item) {
            	if (!$this->_initItem($address, $item, $products) || $item->getQty()<=0) {
            	    $this->_removeItem($address, $item);
            	}
            }
        }

        $address->setGrandTotal($address->getSubtotal());

        return $this;
    }
    
    /**
     * Address item initialization
     *
     * @param  $item
     * @param  $products
     * @return bool
     */
    protected function _initItem($address, $item, $products)
    {
        $product = $products->getItemById($item->getProductId());
        if ($item->getSuperProductId()) {
            $superProduct = $products->getItemById($item->getSuperProductId());
        }
        else {
            $superProduct = null;
        }
        
        if (!$product || !$product->isVisibleInCatalog() || ($superProduct && !$superProduct->isVisibleInCatalog())) {
            return false;
        }
        
        $itemProduct = clone $product;
    	
    	if ($superProduct) {
    	    $itemProduct->setSuperProduct($superProduct);
    	    $item->setSuperProduct($superProduct);
    	}
    	
    	$item->setPrice($itemProduct->getFinalPrice($item->getQty()));
    	$item->setName($itemProduct->getName());
    	$item->setTaxClassId($itemProduct->getTaxClassId());
    	$item->setWeight($itemProduct->getWeight());
    	$item->setStatus($itemProduct->getStatus());
    	$item->setProduct($itemProduct);
        
    	$item->calcRowTotal();
        $address->setSubtotal($address->getSubtotal() + $item->getRowTotal());
        return true;
    }
    
    /**
     * Remove item
     *
     * @param  $address
     * @param  $item
     * @return Mage_Sales_Model_Quote_Address_Total_Subtotal
     */
    protected function _removeItem($address, $item)
    {
	    if ($item instanceof Mage_Sales_Model_Quote_Item) {
	        $address->removeItem($item->getId());
            if ($address->getQuote()) {
                $address->getQuote()->removeItem($item->getId());
            }
	    }
	    elseif ($item instanceof Mage_Sales_Model_Quote_Address_Item) {
	        $address->removeItem($item->getId());
            if ($address->getQuote()) {
                $address->getQuote()->removeItem($item->getQuoteItemId());
            }
	    }
	    
	    return $this;
    }
    
    protected function _getItemsProductCollection($items, $storeId)
    {
    	$productIds = array();
        foreach ($items as $item) {
			$productIds[$item->getProductId()] = $item->getProductId();
			if ($item->getSuperProductId()) {
			    $productIds[$item->getSuperProductId()] = $item->getSuperProductId();
			}
			if ($item->getParentProductId()) {
			    $productIds[$item->getSuperProductId()] = $item->getParentProductId();
			}
        }
        
        $collection = Mage::getResourceModel('catalog/product_collection');
        $collection->getEntity()->setStore($storeId);
        $collection->addAttributeToFilter('entity_id', array('in'=>$productIds))
       	    ->addAttributeToSelect('*')
       	    ->load();
       	return $collection;
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