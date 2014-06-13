<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CatalogInventory\Service\V1;

use Magento\CatalogInventory\Model\Stock;

/**
 * Interface StockStatusServiceInterface
 */
interface StockStatusServiceInterface
{
    /**
     * Retrieve Product Stock Status
     *
     * @param int[] $productIds
     * @param int $websiteId
     * @param int $stockId
     * @return array
     */
    public function getProductStockStatus($productIds, $websiteId, $stockId = Stock::DEFAULT_STOCK_ID);

    /**
     * @param string $sku
     * @return \Magento\CatalogInventory\Service\V1\Data\StockStatus
     * @throw \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getProductStockStatusBySku($sku);

    /**
     * Retrieves a list of SKU's with low inventory qty
     *
     * @param \Magento\CatalogInventory\Service\V1\Data\LowStockCriteria $lowStockCriteria
     * @return \Magento\CatalogInventory\Service\V1\Data\LowStockResult
     */
    public function getLowStockItems($lowStockCriteria);
}
