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
 * @package    Mage_CatalogInventory
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
 
/**
 * Stock item model
 *
 * @category   Mage
 * @package    Mage_CatalogInventory
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_CatalogInventory_Model_Stock_Item extends Mage_Core_Model_Abstract
{
    const XML_PATH_MIN_QTY      = 'cataloginventory/options/min_qty';
    const XML_PATH_BACKORDERS   = 'cataloginventory/options/backorders';
    
    protected function _construct()
    {
        $this->_init('cataloginventory/stock_item');
    }
    
    /**
     * Retrieve stock identifier
     * 
     * @todo multi stock
     * @return int
     */
    public function getStockId()
    {
        return 1;
    }
    
    /**
     * Load item data by product
     *
     * @param   mixed $product
     * @return  Mage_CatalogInventory_Model_Stock_Item
     */
    public function loadByProduct($product)
    {
        if ($product instanceof Mage_Catalog_Model_Product) {
            $product = $product->getId();
        }
        $this->getResource()->loadByProductId($this, $product);
        return $this;
    }
    
    /**
     * Subtract quote item quantity
     *
     * @param   decimal $qty
     * @return  Mage_CatalogInventory_Model_Stock_Item
     */
    public function subtractQty($qty)
    {
        $this->setQty($this->getQty()-$qty);
        if ($this->getBackorders() == Mage_CatalogInventory_Model_Stock::BACKORDERS_NO && $this->getQty() == $this->getMinQty()) {
            $this->setIsInStock(false);
        }
        return $this;
    }
    
    /**
     * Adding stoc data to product
     *
     * @param   Mage_Catalog_Model_Product $product
     * @return  Mage_CatalogInventory_Model_Stock_Item
     */
    public function assignProduct(Mage_Catalog_Model_Product $product)
    {
        if (!$this->getId() || !$this->getProductId()) {
            $this->getResource()->loadByProductId($this, $product->getId());
        }
        
        $product->setStockItem($this);
        $this->setProduct($product);
        $product->setIsSalable($this->getIsInStock());
        return $this;
    }
    
    /**
     * Retrieve minimal quantity available for item status in stock
     *
     * @return decimal
     */
    public function getMinQty()
    {
        if ($this->getUseConfigMinQty()) {
            return (float) Mage::app()->getStore()->getConfig(self::XML_PATH_MIN_QTY);
        }
        return $this->getData('min_qty');
    }
    
    /**
     * Retrieve backorders status
     *
     * @return int
     */
    public function getBackorders()
    {
        if ($this->getUseConfigBackorders()) {
            return (int) Mage::app()->getStore()->getConfig(self::XML_PATH_BACKORDERS);
        }
        return $this->getData('backorders');
    }
    
    /**
     * Check quantity
     *
     * @param   decimal $qty
     * @return  bool
     */
    public function checkQty($qty)
    {
        if (!$this->getIsInStock()) {
            if ($this->getProduct()) {
                Mage::throwException(__('Product "%s" is out of stock.', $this->getProduct()->getName()));
            }
            else {
                Mage::throwException(__('This product is out of stock.'));
            }
        }
        
        if ($this->getQty() - $qty < $this->getMinQty()) {
            switch ($this->getBackorders()) {
                case Mage_CatalogInventory_Model_Stock::BACKORDERS_BELOW:
                case Mage_CatalogInventory_Model_Stock::BACKORDERS_YES:
                    break;
                default:
                    if ($this->getProduct()) {
                        Mage::throwException(
                            __('Requested quantity for "%s" is not available.', 
                            $this->getProduct()->getName())
                        );
                    }
                    else {
                        Mage::throwException(__('Requested quantity is not available.'));
                    }
                    break;
            }
        }
        return true;
    }
    
    /**
     * Checking quote item quantity
     *
     * @param   Mage_Sales_Model_Quote_Item $item
     * @return  Mage_CatalogInventory_Model_Stock_Item
     */
    public function checkQuoteItemQty(Mage_Sales_Model_Quote_Item $item)
    {
        $qty = $item->getQty();
        
        /**
         * Check quontity type
         */
        if ($this->getIsQtyDecimal()) {
            $qty = floatval($qty);
        }
        else {
            $qty = intval($qty);
        }
        
        if ($this->checkQty($qty)) {
            if ($this->getBackorders() == Mage_CatalogInventory_Model_Stock::BACKORDERS_YES) {
                $item->setMessage(__('Backorders.'));
            }
        }
        
        /**
         * Adding stock data to quote item
         */
        $item->addData(array(
            'qty'       => $qty,
            'backorders'=> $this->getBackorders(),
        ));
        
        return $this;
    }
}