<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 */
namespace Magento\CatalogInventory\Api;

/**
 * Interface StockRepositoryInterface
 */
interface StockRepositoryInterface
{
    /**
     * Save Stock data
     *
     * @param \Magento\CatalogInventory\Api\Data\StockInterface $stock
     * @return \Magento\CatalogInventory\Api\Data\StockInterface
     */
    public function save(\Magento\CatalogInventory\Api\Data\StockInterface $stock);

    /**
     * Load Stock data by given stockId and parameters
     *
     * @param string $stockId
     * @return \Magento\CatalogInventory\Api\Data\StockInterface
     */
    public function get($stockId);

    /**
     * Load Stock data collection by given search criteria
     *
     * @param \Magento\CatalogInventory\Api\StockCriteriaInterface $collectionBuilder
     * @return \Magento\CatalogInventory\Api\Data\StockCollectionInterface
     */
    public function getList(StockCriteriaInterface $collectionBuilder);

    /**
     * Delete stock by given stockId
     *
     * @param \Magento\CatalogInventory\Api\Data\StockInterface $stock
     * @return bool
     */
    public function delete(\Magento\CatalogInventory\Api\Data\StockInterface $stock);
}
