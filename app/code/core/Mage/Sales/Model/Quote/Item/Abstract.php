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
 
/**
 * Quote item abstract model
 *
 * @category   Mage
 * @package    Mage_Sales
 * @author     Dmitriy Soroka <dmitriy@varien.com> 
 */
abstract class Mage_Sales_Model_Quote_Item_Abstract extends Mage_Core_Model_Abstract
{
    /**
     * Retrieve store model object
     *
     * @return Mage_Core_Model_Store
     */
    public function getStore()
    {
        if ($this->getQuote()) {
            return $this->getQuote()->getStore();
        }
        return $this->getResource()->getStore();
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
            ->setName($product->getName())
            ->setWeight($product->getWeight())
            ->setTaxClassId($product->getTaxClassId())
            ->setCost($product->getCost());

        if($product->getSuperProduct()) {
        	$this->setSuperProductId($product->getSuperProduct()->getId());
        }
        $this->setProduct($product);

        return $this;
    }
    
    /**
     * Checking item data
     *
     * @return Mage_Sales_Model_Quote_Item_Abstract
     */
    public function checkData()
    {
        $qty = $this->getData('qty');
    	try {
    	    $this->setQty($qty);
    	}
    	catch (Mage_Core_Exception $e){
    	    $this->setHasError(true);
    	    $this->setMessage($e->getMessage());
    	}
    	catch (Exception $e){
    	    $this->setHasError(true);
    	    $item->setMessage(__('Item qty declare error'));
    	}
    	return $this;
    }
    
    /**
     * Calculate item row total price
     *
     * @return Mage_Sales_Model_Quote_Item
     */
    public function calcRowTotal()
    {
        $total = $this->getStore()->roundPrice($this->getCalculationPrice()*$this->getQty());
        $this->setRowTotal($total);
        return $this;
    }

    /**
     * Calculate item row total weight
     *
     * @return Mage_Sales_Model_Quote_Item
     */
    public function calcRowWeight()
    {
        $this->setRowWeight($this->getWeight()*$this->getQty());
        return $this;
    }

    /**
     * Calculate item tax amount
     *
     * @return Mage_Sales_Model_Quote_Item
     */
    public function calcTaxAmount()
    {
        $this->setTaxAmount($this->getRowTotal() * $this->getTaxPercent()/100);
        return $this;
    }
    
    /**
     * Retrieve item price used for calculation
     *
     * @return unknown
     */
    public function getCalculationPrice()
    {
        $price = $this->getData('calculation_price');
        if (is_null($price)) {
            $price = $this->getPrice();
            $price = $this->getCustomPrice() ? $this->getCustomPrice() : $price;
            $price = $this->getStore()->convertPrice($price);
            $this->setData('calculation_price', $price);
        }
        return $price;
    }
}
