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
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Catalog inventory api
 *
 * @category   Mage
 * @package    Mage_CatalogInventory
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_CatalogInventory_Model_Stock_Item_Api extends Mage_Catalog_Model_Api_Resource
{
    public function __construct()
    {
        $this->_storeIdSessionField = 'product_store_id';
    }

    public function items($productIds)
    {
        if (!is_array($productIds)) {
            $productIds = array($productIds);
        }

        $product = Mage::getModel('catalog/product');

        foreach ($productIds as &$productId) {
            if ($newId = $product->getIdBySku($productId)) {
                $productId = $newId;
            }
        }

        $collection = Mage::getModel('catalog/product')
            ->getCollection()
            ->addFieldToFilter('entity_id', array('in'=>$productIds));

        $result = array();

        foreach ($collection as $product) {
            if ($product->getStockItem()) {
                $result[] = array(
                    'product_id'    => $product->getId(),
                    'sku'           => $product->getSku(),
                    'qty'           => $product->getStockItem()->getQty(),
                    'is_in_stock'   => $product->getStockItem()->getIsInStock()
                );
            }
        }

        return $result;
    }

    public function update($productId, $data)
    {
        $product = Mage::getModel('catalog/product');

        if ($newId = $product->getIdBySku($productId)) {
            $productId = $newId;
        }

        $product->setStoreId($this->_getStoreId())
            ->load($productId);

        if (!$product->getId()) {
            $this->_fault('not_exists');
        }

        if (!$stockData = $product->getStockData()) {
            $stockData = array();
        }

        if (isset($data['qty'])) {
            $stockData['qty'] = $data['qty'];
        }

        if (isset($data['is_in_stock'])) {
            $stockData['is_in_stock'] = $data['is_in_stock'];
        }

        $product->setStockData($stockData);

        try {
            $product->save();
        } catch (Mage_Core_Exception $e) {
            $this->_fault('not_updated', $e->getMessage());
        }

        return true;
    }
} // Class Mage_CatalogInventory_Model_Stock_Item_Api End