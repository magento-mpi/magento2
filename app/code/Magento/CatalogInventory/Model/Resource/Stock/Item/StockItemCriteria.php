<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CatalogInventory\Model\Resource\Stock\Item;

use Magento\Framework\Data\AbstractCriteria;

/**
 * Class StockItemCriteria
 */
class StockItemCriteria extends AbstractCriteria implements \Magento\CatalogInventory\Api\StockItemCriteriaInterface
{
    /**
     * @param string $mapper
     */
    public function __construct($mapper = '')
    {
        $this->mapperInterfaceName = $mapper ?: 'Magento\CatalogInventory\Model\Resource\Stock\Item\StockItemCriteriaMapper';
        $this->data['initial_condition'] = true;
    }

    /**
     * @inheritdoc
     */
    public function setStockStatus($storeId = null)
    {
        $this->data['stock_status'] = func_get_args();
    }

    /**
     * @inheritdoc
     */
    public function setStockFilter($stock)
    {
        $this->data['stock_filter'] = $stock;
    }

    /**
     * @inheritdoc
     */
    public function setWebsiteFilter($website)
    {
        $this->data['website_filter'] = $website;
    }

    /**
     * @inheritdoc
     */
    public function setProductsFilter($products)
    {
        $this->data['products_filter'] = $products;
    }

    /**
     * @inheritdoc
     */
    public function setManagedFilter($isStockManagedInConfig)
    {
        $this->data['managed_filter'] = $isStockManagedInConfig;
    }

    /**
     * @inheritdoc
     */
    public function setQtyFilter($comparisonMethod, $qty)
    {
        $this->data['qty_filter'] = [$comparisonMethod, $qty];
    }

    /**
     * Add Criteria object
     *
     * @param \Magento\CatalogInventory\Api\StockItemCriteriaInterface $criteria
     * @return void
     */
    public function addCriteria(\Magento\CatalogInventory\Api\StockItemCriteriaInterface $criteria)
    {
        $this->data[self::PART_CRITERIA_LIST]['list'][] = $criteria;
    }
}
