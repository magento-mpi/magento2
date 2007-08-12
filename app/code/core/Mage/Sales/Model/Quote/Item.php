<?php

class Mage_Sales_Model_Quote_Item extends Mage_Core_Model_Abstract
{
    protected $_quote;
    
    function _construct()
    {
        $this->_init('sales/quote_item');
    }
    
    public function setQuote(Mage_Sales_Model_Quote $quote)
    {
        $this->_quote = $quote;
        return $this;
    }
    
    public function getQuote()
    {
        return $this->_quote;
    }
    
    public function importCatalogProduct(Mage_Catalog_Model_Product $product)
    {
        $this
            ->setProductId($product->getId())
            ->setSku($product->getSku())
            ->setImage($product->getImage())
            ->setName($product->getName())
            ->setWeight($product->getWeight())
            ->setQty($product->getQty())
            ->setPrice($product->getFinalPrice($product->getQty()));
        
        if($product->getParentProduct()) {
        	$this->setParentProductId($product->getParentProduct()->getId());
        }
        
        return $this;
    }
    
    public function calcRowTotal()
    {
        $this->setRowTotal($this->getPrice()*$this->getQty());
        return $this;
    }
    
    public function calcRowWeight()
    {
        $this->setRowWeight($this->getWeight()*$this->getQty());
        return $this;
    }

    public function calcTaxAmount()
    {
        $this->setTaxAmount($this->getRowTotal() * $this->getTaxPercent()/100);
        return $this;
    }
}