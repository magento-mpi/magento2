<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CatalogInventory\Model\Resource\Stock\Status;

use Magento\Framework\Data\AbstractCriteria;

/**
 * Class StockStatusCriteria
 */
class StockStatusCriteria extends AbstractCriteria implements \Magento\CatalogInventory\Api\StockStatusCriteriaInterface
{
    /**
     * @param string $mapper
     */
    public function __construct($mapper = '')
    {
        $this->mapperInterfaceName = $mapper ?: 'Magento\CatalogInventory\Model\Resource\Stock\Status\StockStatusCriteriaMapper';
        $this->data['initial_condition'] = true;
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
    public function setQtyFilter($qty)
    {
        $this->data['qty_filter'] = $qty;
    }

    /**
     * @inheritdoc
     */
    public function addCriteria(\Magento\CatalogInventory\Api\StockStatusCriteriaInterface $criteria)
    {
        $this->data[self::PART_CRITERIA_LIST]['list'][] = $criteria;
    }
}
