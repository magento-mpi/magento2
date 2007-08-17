<?php

class Mage_Catalog_Block_Product extends Mage_Core_Block_Template 
{
	protected $_finalPrice;
	
	public function getProduct()
	{
		if (!$this->getData('product') instanceof Mage_Catalog_Model_Product) {
			if ($this->getData('product')->getProductId()) {
				$productId = $this->getData('product')->getProductId();
			}
			if ($productId) {
				$product = Mage::getModel('catalog/product')->load($productId);
				if ($product) {
					$this->setProduct($product);
				}
			}
		}
		return $this->getData('product');
	}
	
	public function getPrice()
	{
		return $this->getProduct()->getPrice();
	}
	
	public function getFinalPrice()
	{
		if (!isset($this->_finalPrice)) {
			$this->_finalPrice = $this->getProduct()->getFinalPrice();
		}
		return $this->_finalPrice;
	}
	
	public function getPriceHtml($product)
	{
		$this->setTemplate('catalog/product/price.phtml');
		$this->setProduct($product);
		return $this->toHtml();
	}
}