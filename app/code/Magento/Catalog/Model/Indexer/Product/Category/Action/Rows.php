<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

namespace Magento\Catalog\Model\Indexer\Product\Category\Action;

class Rows extends \Magento\Catalog\Model\Indexer\Category\Product\Action\Full
{
    /**
     * Limitation by categories
     *
     * @var int[]
     */
    protected $limitationByCategories;

    /**
     * Refresh entities index
     *
     * @param int[] $entityIds
     * @return $this
     */
    public function execute(array $entityIds = array())
    {
        $this->limitationByCategories = $entityIds;
        return parent::execute();
    }

    /**
     * Return select for remove unnecessary data
     *
     * @param int[] $rootCatIds
     * @return \Magento\DB\Select
     */
    protected function getSelectUnnecessaryData(array $rootCatIds)
    {
        $select = parent::getSelectUnnecessaryData($rootCatIds);
        return $select->where($this->getMainTable() . '.category_id IN (?)', $this->limitationByCategories);
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
        return $select->where('cc.entity_id IN (?)', $this->limitationByCategories);
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
        return $select->where('cc.entity_id IN (?)', $this->limitationByCategories);
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
        return $select->where($store->getRootCategoryId() . ' IN (?)', $this->limitationByCategories);
    }

    /**
     * Return selects cut by min and max
     *
     * @param \Magento\DB\Select $select
     * @param string $field
     * @param int $range
     * @return \Magento\DB\Select[]
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    protected function prepareSelectsByRange(\Magento\DB\Select $select, $field, $range = self::RANGE_CATEGORY_STEP)
    {
        return array($select);
    }
}
