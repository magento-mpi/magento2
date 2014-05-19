<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CatalogInventory\Service\V1;

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
    public function getProductStockStatus($productIds, $websiteId, $stockId = 1);
}
