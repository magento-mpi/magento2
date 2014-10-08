<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CatalogInventory\Service\V1;

/**
 * Stock item interface
 */
interface StockItemServiceInterface
{
    /**
     * @param int $productId
     * @return \Magento\CatalogInventory\Service\V1\Data\StockItem
     */
    public function getStockItem($productId);

    /**
     * @param string $productSku
     * @return \Magento\CatalogInventory\Service\V1\Data\StockItem
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getStockItemBySku($productSku);

    /**
     * @param \Magento\CatalogInventory\Service\V1\Data\StockItem $stockItem
     * @return \Magento\CatalogInventory\Service\V1\Data\StockItem
     */
    public function saveStockItem($stockItem);

    /**
     * @param string $productSku
     * @param \Magento\CatalogInventory\Service\V1\Data\StockItemDetails $stockItemDetailsDo
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function saveStockItemBySku($productSku, Data\StockItemDetails $stockItemDetailsDo);

    /**
     * @param int $productId
     * @return int
     */
    public function getMinSaleQty($productId);

    /**
     * @param int $productId
     * @return int
     */
    public function getMaxSaleQty($productId);

    /**
     * Retrieve minimal quantity available for item status in stock
     *
     * @param $productId
     * @return float
     */
    public function getMinStockQty($productId);

    /**
     * @param int $productId
     * @return bool
     */
    public function getEnableQtyIncrements($productId);

    /**
     * @param int $productId
     * @return int
     */
    public function getQtyIncrements($productId);

    /**
     * @param int $productId
     * @return int mixed
     */
    public function getManageStock($productId);

    /**
     * @param int $productId
     * @param int $qty
     * @return bool
     */
    public function suggestQty($productId, $qty);

    /**
     * @param int $productId
     * @param int $qty
     * @param int $summaryQty
     * @param int $origQty
     * @return int
     */
    public function checkQuoteItemQty($productId, $qty, $summaryQty, $origQty = 0);

    /**
     * @param int $productId
     * @param int|null $qty
     * @return bool
     */
    public function verifyStock($productId, $qty = null);

    /**
     * @param int $productId
     * @param int|null $qty
     * @return bool
     */
    public function verifyNotification($productId, $qty = null);

    /**
     * @param int $productId
     * @return bool
     */
    public function getIsInStock($productId);

    /**
     * @param int $productId
     * @return int
     */
    public function getStockQty($productId);

    /**
     * @param int $productTypeId
     * @return bool
     */
    public function isQty($productTypeId);

    /**
     * @param int|null $filter
     * @return bool
     */
    public function getIsQtyTypeIds($filter = null);

    /**
     * @param int $stockData
     * @return array
     */
    public function processIsInStock($stockData);
}
