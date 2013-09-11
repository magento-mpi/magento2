<?php
/**
 * {license_notice}
 *
 * Catalog inventory api V2
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CatalogInventory\Model\Stock\Item\Api;

class V2 extends \Magento\CatalogInventory\Model\Stock\Item\Api
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
        /** @var $product \Magento\Catalog\Model\Product */
        $product = \Mage::getModel('Magento\Catalog\Model\Product');
        $productId = $product->getIdBySku($productId) ?: $productId;

        $product->setStoreId($this->_getStoreId())
            ->load($productId);

        if (!$product->getId()) {
            $this->_fault('not_exists');
        }

        /** @var $stockItem \Magento\CatalogInventory\Model\Stock\Item */
        $stockItem = $product->getStockItem();
        $stockData = array_replace($stockItem->getData(), (array)$data);
        $stockItem->setData($stockData);

        try {
            $stockItem->save();
        } catch (\Magento\Core\Exception $e) {
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
