<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Stock collection interface
 */
namespace Magento\CatalogInventory\Api\Data;

use Magento\Framework\Data\SearchResultInterface;

/**
 * Interface StockCollectionInterface
 * @package Magento\CatalogInventory\Api\Data
 */
interface StockCollectionInterface extends SearchResultInterface
{
    /**
     * Get items
     *
     * @return \Magento\CatalogInventory\Api\Data\StockInterface[]
     */
    public function getItems();
}
