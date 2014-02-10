<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

namespace Magento\Catalog\Model\Indexer\Category\Product\Action;

class Rows extends Full
{
    /**
     * Limitation by products
     *
     * @var int[]
     */
    protected $limitationByProducts;

    /**
     * Refresh entities index
     *
     * @param int[] $entityIds
     * @return $this
     */
    public function execute(array $entityIds = array())
    {
        $this->limitationByProducts = $entityIds;
        return parent::execute();
    }

    /**
     * Return select for remove unnecessary data
     *
     * @return \Magento\DB\Select
     */
    protected function getSelectUnnecessaryData()
    {
        $select = parent::getSelectUnnecessaryData();
        return $select->where($this->getMainTable() . '.product_id IN (?)', $this->limitationByProducts);
    }

    /**
     * Retrieve select for reindex products of non anchor categories
     *
     * @param \Magento\Core\Model\Store $store
     * @return \Magento\DB\Select
     */
    protected function getNonAnchorCategoriesSelect(\Magento\Core\Model\Store $store)
    {
        $select = parent::getNonAnchorCategoriesSelect($store);
        return $select->where('ccp.product_id IN (?)', $this->limitationByProducts);
    }

    /**
     * Retrieve select for reindex products of non anchor categories
     *
     * @param \Magento\Core\Model\Store $store
     * @return \Magento\DB\Select
     */
    protected function getAnchorCategoriesSelect(\Magento\Core\Model\Store $store)
    {
        $select = parent::getAnchorCategoriesSelect($store);
        return $select->where('ccp.product_id IN (?)', $this->limitationByProducts);
    }

    /**
     * Get select for all products
     *
     * @param \Magento\Core\Model\Store $store
     * @return \Magento\DB\Select
     */
    protected function getAllProducts(\Magento\Core\Model\Store $store)
    {
        $select = parent::getAllProducts($store);
        return $select->where('cp.entity_id IN (?)', $this->limitationByProducts);
    }

    /**
     * Check whether select ranging is needed
     *
     * @return bool
     */
    protected function isRangingNeeded()
    {
        return false;
    }
}
