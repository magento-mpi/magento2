<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\CatalogPermissions\Model\Indexer\Product\Action;

use Magento\CatalogPermissions\Model\Indexer\AbstractAction;

class Rows extends AbstractAction
{
    /**
     * Limitation by products
     *
     * @var int[]
     */
    protected $entityIds;

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
     * Run reindexation
     *
     * @return void
     */
    protected function reindex()
    {
        $this->populateProductIndex();
        $this->fixProductPermissions();
    }

    /**
     * Remove index entries before reindexation
     *
     * @return void
     */
    protected function removeObsoleteIndexData()
    {
        $this->getWriteAdapter()->delete(
            $this->getProductIndexTempTable(),
            ['product_id IN (?)' => $this->entityIds]
        );
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
        return $this->entityIds;
    }

    /**
     * Retrieve category list
     *
     * Returns [entity_id, path] pairs.
     *
     * @return array
     */
    protected function getCategoryList()
    {
        return [];
    }
}
