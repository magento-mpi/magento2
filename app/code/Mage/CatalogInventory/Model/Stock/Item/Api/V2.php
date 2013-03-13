<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_CatalogInventory
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Catalog inventory api V2
 *
 * @category   Mage
 * @package    Mage_CatalogInventory
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_CatalogInventory_Model_Stock_Item_Api_V2 extends Mage_CatalogInventory_Model_Stock_Item_Api
{
    public function update($productId, $data)
    {
        $product = Mage::getModel('Mage_Catalog_Model_Product');

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

        if (isset($data->qty)) {
            $stockData['qty'] = $data->qty;
        }

        if (isset($data->is_in_stock)) {
            $stockData['is_in_stock'] = $data->is_in_stock;
        }

        if (isset($data->manage_stock)) {
            $stockData['manage_stock'] = $data->manage_stock;
        }

        if (isset($data->use_config_manage_stock)) {
            $stockData['use_config_manage_stock'] = $data->use_config_manage_stock;
        }

        if (isset($data->use_config_backorders)) {
            $stockData['use_config_backorders'] = $data->use_config_backorders;
        }

        if (isset($data->backorders)) {
            $stockData['backorders'] = $data->backorders;
        }

        $product->setStockData($stockData);

        try {
            $product->save();
        } catch (Mage_Core_Exception $e) {
            $this->_fault('not_updated', $e->getMessage());
        }

        return true;
    }
}
