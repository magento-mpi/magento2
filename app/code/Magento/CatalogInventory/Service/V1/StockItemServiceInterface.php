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
     * @param \Magento\CatalogInventory\Service\V1\Data\StockItem $stockItem
     * @return \Magento\CatalogInventory\Service\V1\Data\StockItem
     */
    public function saveStockItem($stockItem);

    /**
     * @param int $productId
     * @param int $qty
     * @return $this
     */
    public function subtractQty($productId, $qty);

    /**
     * @param int $productId
     * @return bool
     */
    public function canSubtractQty($productId);

    /**
     * @param int $productId
     * @param int $qty
     * @return $this
     */
    public function addQty($productId, $qty);

    /**
     * @param int $productId
     * @return int
     */
    public function getMinQty($productId);

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
     * @param int $productId
     * @return int
     */
    public function getNotifyStockQty($productId);

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
     * @return int
     */
    public function getBackorders($productId);

    /**
     * @param int $productId
     * @return int mixed
     */
    public function getManageStock($productId);

    /**
     * @param int $productId
     * @return bool
     */
    public function getCanBackInStock($productId);

    /**
     * @param int $productId
     * @param int $qty
     * @return bool
     */
    public function checkQty($productId, $qty);

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
     * @param int $productId
     * @param int $qty
     * @return bool
     */
    public function checkQtyIncrements($productId, $qty);

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
}
