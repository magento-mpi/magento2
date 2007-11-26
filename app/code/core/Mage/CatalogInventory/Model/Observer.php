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
 * Catalog inventory module observer
 *
 * @category   Mage
 * @package    Mage_CatalogInventory
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_CatalogInventory_Model_Observer
{
    /**
     * Adding stock information to product
     *
     * @param   Varien_Event_Observer $observer
     * @return  Mage_CatalogInventory_Model_Observer
     */
    public function addInventoryData($observer)
    {
        $product = $observer->getEvent()->getProduct();
        Mage::getModel('cataloginventory/stock_item')->assignProduct($product);
        return $this;
    }
    
    /**
     * Saving product inventory data
     *
     * @param   Varien_Event_Observer $observer
     * @return  Mage_CatalogInventory_Model_Observer
     */
    public function saveInventoryData($observer)
    {
        $product = $observer->getEvent()->getProduct();
        if (is_null($product->getStockData())) {
            return $this;
        }
        
        $item = $product->getStockItem();
        if (!$item) {
            $item = Mage::getModel('cataloginventory/stock_item');
        }
        $this->_prepareItemForSave($item, $product);
        $item->save();
        return $this;
    }
    
    protected function _prepareItemForSave($item, $product)
    {
        $item->addData($product->getStockData())
            ->setProductId($product->getId())
            ->setStockId($item->getStockId());
        if (is_null($product->getData('stock_data/use_config_min_qty'))) {
            $item->setData('use_config_min_qty', false);
        }
        if (is_null($product->getData('stock_data/use_config_backorders'))) {
            $item->setData('use_config_backorders', false);
        }
        return $this;
        
    }
}
