<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Stock Item collection interface
 */
namespace Magento\CatalogInventory\Api\Data;

use Magento\Framework\Data\SearchResultInterface;

/**
 * Interface StockItemCollectionInterface
 * @package Magento\CatalogInventory\Api\Data
 */
interface StockItemCollectionInterface extends SearchResultInterface
{
    /**
     * Get items
     *
     * @return \Magento\CatalogInventory\Api\Data\StockItemInterface[]
     */
    public function getItems();
}
