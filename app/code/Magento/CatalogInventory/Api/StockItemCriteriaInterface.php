<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CatalogInventory\Api;

/**
 * Interface StockItemCriteriaInterface
 */
interface StockItemCriteriaInterface extends \Magento\Framework\Api\CriteriaInterface
{
    /**
     * Add Criteria object
     *
     * @param \Magento\CatalogInventory\Api\StockItemCriteriaInterface $criteria
     * @return void
     */
    public function addCriteria(\Magento\CatalogInventory\Api\StockItemCriteriaInterface $criteria);

    /**
     * Join Stock Status to collection
     *
     * @param int $storeId
     * @return void
     */
    public function setStockStatus($storeId = null);

    /**
     * Add stock filter to collection
     *
     * @param \Magento\CatalogInventory\Api\Data\StockInterface|string|array $stock
     * @return void
     */
    public function setStockFilter($stock);

    /**
     * Add website filter to collection
     *
     * @param array|int|object $website
     * @return void
     */
    public function setWebsiteFilter($website);

    /**
     * Add product filter to collection
     *
     * @param array|int|object $products
     * @return void
     */
    public function setProductsFilter($products);

    /**
     * Add Managed Stock products filter to collection
     *
     * @param bool $isStockManagedInConfig
     * @return void
     */
    public function setManagedFilter($isStockManagedInConfig);

    /**
     * Add filter by quantity to collection
     *
     * @param string $comparisonMethod
     * @param float $qty
     * @return void
     */
    public function setQtyFilter($comparisonMethod, $qty);
}
