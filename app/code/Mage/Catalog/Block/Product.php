<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */


class Mage_Catalog_Block_Product extends Magento_Core_Block_Template
{
    protected $_finalPrice = array();

    public function getProduct()
    {
        if (!$this->getData('product') instanceof Mage_Catalog_Model_Product) {
            if ($this->getData('product')->getProductId()) {
                $productId = $this->getData('product')->getProductId();
            }
            if ($productId) {
                $product = Mage::getModel('Mage_Catalog_Model_Product')->load($productId);
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
        if (!isset($this->_finalPrice[$this->getProduct()->getId()])) {
            $this->_finalPrice[$this->getProduct()->getId()] = $this->getProduct()->getFinalPrice();
        }
        return $this->_finalPrice[$this->getProduct()->getId()];
    }

    public function getPriceHtml($product)
    {
        $this->setTemplate('product/price.phtml');
        $this->setProduct($product);
        return $this->toHtml();
    }
}
