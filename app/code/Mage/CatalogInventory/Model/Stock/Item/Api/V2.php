<?php
/**
 * {license_notice}
 *
 * Catalog inventory api V2
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_CatalogInventory_Model_Stock_Item_Api_V2 extends Mage_CatalogInventory_Model_Stock_Item_Api
{
    /**
     * Update product stock data
     *
     * @param int   $productId
     * @param array $data
     * @return bool
     */
    public function update($productId, $data)
    {
        /** @var $product Mage_Catalog_Model_Product */
        $product = Mage::getModel('Mage_Catalog_Model_Product');
        $productId = $product->getIdBySku($productId) ?: $productId;

        $product->setStoreId($this->_getStoreId())
            ->load($productId);

        if (!$product->getId()) {
            $this->_fault('not_exists');
        }

        /** @var $stockItem Mage_CatalogInventory_Model_Stock_Item */
        $stockItem = $product->getStockItem();
        $stockData = array_replace($stockItem->getData(), (array)$data);
        $stockItem->setData($stockData);

        try {
            $stockItem->save();
        } catch (Mage_Core_Exception $e) {
            $this->_fault('not_updated', $e->getMessage());
        }

        return true;
    }

    /**
     * Update stock data of multiple products at once
     *
     * @param array $productIds
     * @param array $productsData
     * @return boolean
     */
    public function multiUpdate($productIds, $productsData)
    {
        if (count($productIds) != count($productsData)) {
            $this->_fault('multi_update_not_match');
        }

        $productsData = (array)$productsData;

        foreach ($productIds as $index => $productId) {
            $this->update($productId, $productsData[$index]);
        }

        return true;
    }
}
