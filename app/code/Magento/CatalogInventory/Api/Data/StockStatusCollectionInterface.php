<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CatalogInventory\Api\Data;

use Magento\Framework\Data\SearchResultInterface;

/**
 * Stock Status collection interface
 */
interface StockStatusCollectionInterface extends SearchResultInterface
{
    /**
     * Get items
     *
     * @return \Magento\CatalogInventory\Api\Data\StockStatusInterface[]
     */
    public function getItems();
}
