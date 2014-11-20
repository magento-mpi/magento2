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
 */
interface StockManagementInterface
{
    /**
     * Subtract product qtys from stock.
     * Return array of items that require full save
     *
     * @param string[] $items
     * @param int $websiteId
     * @return \Magento\CatalogInventory\Api\Data\StockItemInterface[]
     * @throws \Magento\Framework\Model\Exception
     */
    public function registerProductsSale($items, $websiteId = null);

    /**
     * @param string[] $items
     * @param int $websiteId
     * @return bool
     */
    public function revertProductsSale(array $items, $websiteId = null);

    /**
     * Get back to stock (when order is canceled or whatever else)
     *
     * @param int $productId
     * @param float $qty
     * @param int $websiteId
     * @return bool
     */
    public function backItemQty($productId, $qty, $websiteId = null);
}
