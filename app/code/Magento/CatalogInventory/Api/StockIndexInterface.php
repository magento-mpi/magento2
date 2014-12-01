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
     * @param int|null $productId
     * @param int|null $websiteId
     * @return bool
     */
    public function rebuild($productId = null, $websiteId = null);
}
