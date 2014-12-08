<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CatalogPermissions\Model\Indexer\Category\Action;

class Rows extends \Magento\CatalogPermissions\Model\Indexer\AbstractAction
{
    /**
     * Limitation by categories
     *
     * @var int[]
     */
    protected $entityIds;

    /**
     * Affected product IDs
     *
     * @var int[]
     */
    protected $productIds;

    /**
     * Refresh entities index
     *
     * @param int[] $entityIds
     * @param bool $useIndexTempTable
     * @return void
     */
    public function execute(array $entityIds = [], $useIndexTempTable = false)
    {
        if ($entityIds) {
            $this->entityIds = $entityIds;
            $this->useIndexTempTable = $useIndexTempTable;

            $this->removeObsoleteIndexData();

            $this->reindex();
        }
    }

    /**
     * Remove index entries before reindexation
     *
     * @return void
     */
    protected function removeObsoleteIndexData()
    {
        $this->getWriteAdapter()->delete($this->getIndexTempTable(), ['category_id IN (?)' => $this->entityIds]);
        $this->getWriteAdapter()->delete(
            $this->getProductIndexTempTable(),
            ['product_id IN (?)' => $this->getProductList()]
        );
    }

    /**
     * Retrieve category list
     *
     * Return entity_id, path pairs.
     *
     * @return array
     */
    protected function getCategoryList()
    {
        $select = $this->getReadAdapter()->select()->from(
            $this->getTable('catalog_category_entity'),
            ['path']
        )->where(
            'entity_id IN (?)',
            $this->entityIds
        );

        $categoriesPathList = $this->getReadAdapter()->fetchCol($select);

        $select = $this->getReadAdapter()->select()->from(
            $this->getTable('catalog_category_entity'),
            ['entity_id', 'path']
        )->order(
            'level ASC'
        );

        $calculatedEntityIds = [];
        foreach ($categoriesPathList as $path) {
            $select->where('path LIKE ?', $path . '/%');
            $calculatedEntityIds = array_merge($calculatedEntityIds, explode('/', $path));
        }

        $select->orWhere('entity_id IN (?)', array_unique($calculatedEntityIds));

        return $this->getReadAdapter()->fetchPairs($select);
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

    /**
     * Return list of product IDs to reindex
     *
     * @return int[]
     */
    protected function getProductList()
    {
        if (is_null($this->productIds)) {
            $select = $this->getReadAdapter()->select()->from(
                $this->getTable('catalog_category_product'),
                'product_id'
            )->distinct(
                true
            )->where(
                'category_id IN (?)',
                $this->entityIds
            );

            $this->productIds = $this->getReadAdapter()->fetchCol($select);
        }
        return $this->productIds;
    }
}
