<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CatalogInventory\Api;

/**
 * Interface StockIndexInterface
 */
interface StockIndexInterface
{
    /**
     * Rebuild stock index of the given website
     *
     * @param int $productId
     * @param int $websiteId
     * @return bool
     */
    public function rebuild($productId = null, $websiteId = null);
}
