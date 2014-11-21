<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CatalogInventory\Api;

/**
 * Interface StockManagementInterface
 */
interface StockManagementInterface
{
    /**
     * Subtract product qtys from stock.
     * Return array of items that require full save
     *
     * @attention! this method does not work with API
     *
     * @param array {this argument should work with key => value pair} $items
     * @param int $websiteId
     * @return \Magento\CatalogInventory\Api\Data\StockItemInterface[]
     * @throws \Magento\Framework\Model\Exception
     */
    public function registerProductsSale($items, $websiteId = null);

    /**
     * @attention! this method does not work with API
     *
     * @param array {this argument should work with key => value pair} $items
     * @param int $websiteId
     * @return void
     */
    public function revertProductsSale($items, $websiteId = null);

    /**
     * Get back to stock (when order is canceled or whatever else)
     *
     * @param int $productId
     * @param int|float $qty
     * @param int $websiteId
     * @return void
     */
    public function backItemQty($productId, $qty, $websiteId = null);
}
