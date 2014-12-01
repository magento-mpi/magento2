<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CatalogInventory\Api;

/**
 * Interface StockConfigurationInterface
 */
interface StockConfigurationInterface
{
    /**
     * Retrieve Default Website ID
     *
     * @return int
     */
    public function getDefaultWebsiteId();

    /**
     * @param int|null $filter
     * @return int[]
     */
    public function getIsQtyTypeIds($filter = null);

    /**
     * Check if Stock Management is applicable for the given Product Type
     *
     * @param int $productTypeId
     * @return bool
     */
    public function isQty($productTypeId);

    /**
     * Check if is possible subtract value from item qty
     *
     * @param int|null $storeId
     * @return bool
     */
    public function canSubtractQty($storeId = null);

    /**
     * @param int|null $storeId
     * @return float
     */
    public function getMinQty($storeId = null);

    /**
     * @param int|null $storeId
     * @param int|null $customerGroupId
     * @return float
     */
    public function getMinSaleQty($storeId = null, $customerGroupId = null);

    /**
     * @param int|null $storeId
     * @return float
     */
    public function getMaxSaleQty($storeId = null);

    /**
     * @param int|null $storeId
     * @return float
     */
    public function getNotifyStockQty($storeId = null);

    /**
     * Retrieve whether Quantity Increments is enabled
     *
     * @param int|null $storeId
     * @return bool
     */
    public function getEnableQtyIncrements($storeId = null);

    /**
     * @param int|null $storeId
     * @return int
     */
    public function getQtyIncrements($store = null);

    /**
     * Retrieve backorders status
     *
     * @param int|null $storeId
     * @return int
     */
    public function getBackorders($storeId = null);

    /**
     * Retrieve Manage Stock data wrapper
     *
     * @param int|null $storeId
     * @return int
     */
    public function getManageStock($storeId = null);

    /**
     * Retrieve can Back in stock
     *
     * @param int|null $storeId
     * @return bool
     */
    public function getCanBackInStock($storeId = null);

    /**
     * Display out of stock products option
     *
     * @param int|null $storeId
     * @return bool
     */
    public function isShowOutOfStock($storeId = null);

    /**
     * Check if credit memo items auto return option is enabled
     *
     * @param int|null $storeId
     * @return bool
     */
    public function isAutoReturnEnabled($storeId = null);

    /**
     * Get 'Display product stock status' option value
     * Shows if it is necessary to show product stock status ('in stock'/'out of stock')
     *
     * @param int|null $storeId
     * @return bool
     */
    public function isDisplayProductStockStatus($storeId = null);

    /**
     * @param string $field
     * @param int|null $storeId
     * @return string
     */
    public function getDefaultConfigValue($field, $storeId = null);

    /**
     * Retrieve inventory item options (used in config)
     *
     * @return string[]
     */
    public function getConfigItemOptions();
}
