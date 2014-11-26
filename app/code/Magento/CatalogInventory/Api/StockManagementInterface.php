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
 * @package Magento\CatalogInventory\Api
 * @api
 */
interface StockManagementInterface
{
    /**
     * Subtract product qtys from stock.
     * Return array of items that require full save
     *
     * @param array $items
     * @param int $websiteId
     * @return \Magento\CatalogInventory\Api\Data\StockItemInterface[]
     * @throws \Magento\Framework\Model\Exception
     */
    public function registerProductsSale($items, $websiteId = null);

    /**
     * @param array $items
     * @param int $websiteId
     * @return void
     */
    public function revertProductsSale(array $items, $websiteId = null);

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
