<?php
/**
 * {license_notice}
 *   
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CatalogPermissions\Model\Indexer\Category\Action;

class Full extends \Magento\CatalogPermissions\Model\Indexer\Category\AbstractAction
{
    /**
     * Refresh entities index
     *
     * @return void
     */
    public function execute()
    {
        $this->clearIndexTempTable();

        $this->reindex();

        $this->publishIndexData();

        $this->removeObsoleteIndexData();
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
        $select = $this->getReadAdapter()->select()
            ->from($this->getTable('catalog_category_entity'), ['entity_id', 'path'])
            ->order('level ASC');

        return $this->getReadAdapter()->fetchPairs($select);
    }

    /**
     * Clear all index temporary data
     *
     * @return void
     */
    protected function clearIndexTempTable()
    {
        $this->getWriteAdapter()->delete(
            $this->getIndexTempTable()
        );
    }

    /**
     * Publish data from temporary index table to index
     *
     * @return void
     */
    protected function publishIndexData()
    {
        $select = $this->getWriteAdapter()->select()
            ->from($this->getIndexTempTable());

        $queries = $this->prepareSelectsByRange($select, 'category_id');

        foreach ($queries as $query) {
            $this->getWriteAdapter()->query(
                $this->getWriteAdapter()->insertFromSelect(
                    $query,
                    $this->getIndexTable(),
                    ['category_id', 'website_id', 'customer_group_id',
                        'grant_catalog_category_view', 'grant_catalog_product_price', 'grant_checkout_items'],
                    \Magento\DB\Adapter\AdapterInterface::INSERT_ON_DUPLICATE
                )
            );
        }
    }

    /**
     * Remove unnecessary data
     *
     * @return void
     */
    protected function removeObsoleteIndexData()
    {
        $query = $this->getWriteAdapter()->select()
            ->from(['m' => $this->getIndexTable()])
            ->joinLeft(
                ['t' => $this->getIndexTempTable()],
                'm.category_id = t.category_id'
                . ' AND m.website_id = t.website_id'
                . ' AND m.customer_group_id = t.customer_group_id'
            )
            ->where('t.category_id IS NULL');

        $this->getWriteAdapter()->query(
            $this->getWriteAdapter()->deleteFromSelect($query, $this->getIndexTable())
        );
    }

    /**
     * Check whether select ranging is needed
     *
     * @return bool
     */
    protected function isRangingNeeded()
    {
        return true;
    }
}
