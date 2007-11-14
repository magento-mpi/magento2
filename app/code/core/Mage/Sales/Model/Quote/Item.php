<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Sales
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


class Mage_Sales_Model_Quote_Item extends Mage_Core_Model_Abstract
{
    /**
     * Quote model object
     *
     * @var Mage_Sales_Model_Quote
     */
    protected $_quote;

    function _construct()
    {
        $this->_init('sales/quote_item');
    }

    /**
     * Declare quote model object
     *
     * @param   Mage_Sales_Model_Quote $quote
     * @return  Mage_Sales_Model_Quote_Item
     */
    public function setQuote(Mage_Sales_Model_Quote $quote)
    {
        $this->_quote = $quote;
        return $this;
    }

    /**
     * Retrieve quote model object
     *
     * @return Mage_Sales_Model_Quote
     */
    public function getQuote()
    {
        return $this->_quote;
    }

    /**
     * Import item data from product model object
     *
     * @param   Mage_Catalog_Model_Product $product
     * @return  Mage_Sales_Model_Quote_Item
     */
    public function importCatalogProduct(Mage_Catalog_Model_Product $product)
    {
        $this->setProductId($product->getId())
            ->setSku($product->getSku())
            ->setImage($product->getImage())
            ->setName($product->getName())
            ->setWeight($product->getWeight())
            ->setTaxClassId($product->getTaxClassId())
            ->setQty($product->getQuoteQty())
            ->setCost($product->getCost());

        if($product->getSuperProduct()) {
        	$this->setSuperProductId($product->getSuperProduct()->getId());
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
            ->setSuperProductId($item->getSuperProductId()) // TODO
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