<?php

class Mage_Sales_Model_Quote_Item extends Mage_Core_Model_Abstract
{
    /**
     * Enter description here...
     *
     * @var Mage_Sales_Model_Quote
     */
    protected $_quote;

    function _construct()
    {
        $this->_init('sales/quote_item');
    }

    /**
     * Enter description here...
     *
     * @param Mage_Sales_Model_Quote $quote
     * @return Mage_Sales_Model_Quote_Item
     */
    public function setQuote(Mage_Sales_Model_Quote $quote)
    {
        $this->_quote = $quote;
        return $this;
    }

    /**
     * Enter description here...
     *
     * @return Mage_Sales_Model_Quote
     */
    public function getQuote()
    {
        return $this->_quote;
    }

    /**
     * Enter description here...
     *
     * @param Mage_Catalog_Model_Product $product
     * @return Mage_Sales_Model_Quote_Item
     */
    public function importCatalogProduct(Mage_Catalog_Model_Product $product)
    {
        $this
            ->setProductId($product->getId())
            ->setSku($product->getSku())
            ->setImage($product->getImage())
            ->setName($product->getName())
            ->setWeight($product->getWeight())
            ->setTaxClassId($product->getTaxClassId())
            ->setQty($product->getQty())
            ->setPrice($product->getFinalPrice($product->getQty()))
            ->setCost($product->getCost());

        if($product->getParentProduct()) {
        	$this->setParentProductId($product->getParentProduct()->getId());
        }

        return $this;
    }

    /**
     * Enter description here...
     *
     * @return Mage_Sales_Model_Quote_Item
     */
    public function calcRowTotal()
    {
        $this->setRowTotal($this->getPrice()*$this->getQty());
        return $this;
    }

    /**
     * Enter description here...
     *
     * @return Mage_Sales_Model_Quote_Item
     */
    public function calcRowWeight()
    {
        $this->setRowWeight($this->getWeight()*$this->getQty());
        return $this;
    }

    /**
     * Enter description here...
     *
     * @return Mage_Sales_Model_Quote_Item
     */
    public function calcTaxAmount()
    {
        $this->setTaxAmount($this->getRowTotal() * $this->getTaxPercent()/100);
        return $this;
    }

    /**
     * Enter description here...
     *
     * @param Mage_Sales_Model_Order_Item $item
     * @return Mage_Sales_Model_Quote_Item
     */
    public function importOrderItem(Mage_Sales_Model_Order_Item $item)
    {
        $this->setProductId($item->getProductId())
            ->setParentProductId($item->getParentProductId()) // TODO
            ->setSku($item->getSku())
            ->setImage($item->getImage())
            ->setName($item->getName())
            ->setDescription($item->getDescription())
            ->setWeight($item->getWeight()) // TODO
            ->setQty($item->getQtyToShip())
            ->setPrice($item->getPrice())
            ->setCost($item->getCost()) // TODO
        ;
        return $this;
    }

    public function getProduct()
    {
    	if (!$this->hasData('product') && $this->getProductId()) {
    		$this->setProduct(Mage::getModel('catalog/product')->load($this->getProductId()));
    	}
    	return $this->getData('product');
    }
}