<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

namespace Magento\Catalog\Model\Indexer\Category\Product\Action;

class Full extends \Magento\Catalog\Model\Indexer\Category\Product\AbstractAction
{
    /**
     * Refresh entities index
     *
     * @return $this
     */
    public function execute()
    {
        $this->createTmpTable();

        $this->reindex();

        $this->publishData();
        $this->removeUnnecessaryData();
        $this->clearTmpData();

        return $this;
    }

    /**
     * Create temporary index table
     */
    protected function createTmpTable()
    {
        $table = $this->getWriteAdapter()
            ->newTable($this->getMainTmpTable())
            ->addColumn(
                'category_id',
                \Magento\DB\Ddl\Table::TYPE_INTEGER,
                null,
                [
                    'unsigned'  => true,
                    'nullable'  => false,
                    'primary'   => true,
                    'default'   => '0',
                ],
                'Category ID'
            )
            ->addColumn(
                'product_id',
                \Magento\DB\Ddl\Table::TYPE_INTEGER,
                null,
                [
                    'unsigned'  => true,
                    'nullable'  => false,
                    'primary'   => true,
                    'default'   => '0',
                ],
                'Product ID'
            )
            ->addColumn(
                'position',
                \Magento\DB\Ddl\Table::TYPE_INTEGER,
                null,
                [
                    'unsigned'  => false,
                    'nullable'  => true,
                    'default'   => null,
                ],
                'Position'
            )
            ->addColumn(
                'is_parent',
                \Magento\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                [
                    'unsigned'  => true,
                    'nullable'  => false,
                    'default'   => '0',
                ],
                'Is Parent'
            )
            ->addColumn(
                'store_id',
                \Magento\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                [
                    'unsigned'  => true,
                    'nullable'  => false,
                    'primary'   => true,
                    'default'   => '0',
                ],
                'Store ID'
            )
            ->addColumn(
                'visibility',
                \Magento\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                [
                    'unsigned'  => true,
                    'nullable'  => false,
                ],
                'Visibility'
            )
            ->setComment('Catalog Category Product Index Tmp');

        $this->getWriteAdapter()->dropTable($this->getMainTmpTable());
        $this->getWriteAdapter()->createTable($table);
    }

    /**
     * Return select for remove unnecessary data
     *
     * @return \Magento\DB\Select
     */
    protected function getSelectUnnecessaryData()
    {
        return $this->getWriteAdapter()->select()
            ->from($this->getMainTable(), [])
            ->joinLeft(
                ['t' => $this->getMainTmpTable()],
                $this->getMainTable() . '.category_id = t.category_id AND '
                . $this->getMainTable() . '.store_id = t.store_id AND '
                . $this->getMainTable() . '.product_id = t.product_id',
                []
            )
            ->where('t.category_id IS NULL');
    }

    /**
     * Remove unnecessary data
     */
    protected function removeUnnecessaryData()
    {
        $this->getWriteAdapter()->query(
            $this->getWriteAdapter()->deleteFromSelect(
                $this->getSelectUnnecessaryData(), $this->getMainTable()
            )
        );
    }

    /**
     * Publish data from tmp to index
     */
    protected function publishData()
    {
        $select = $this->getWriteAdapter()->select()
            ->from($this->getMainTmpTable());

        $queries = $this->prepareSelectsByRange($select, 'category_id');

        foreach ($queries as $query) {
            $this->getWriteAdapter()->query(
                $this->getWriteAdapter()->insertFromSelect(
                    $query,
                    $this->getMainTable(),
                    ['category_id', 'product_id', 'position', 'is_parent', 'store_id', 'visibility'],
                    \Magento\DB\Adapter\AdapterInterface::INSERT_ON_DUPLICATE
                )
            );
        }
    }

    /**
     * Clear all index data
     */
    protected function clearTmpData()
    {
        $this->getWriteAdapter()->dropTable($this->getMainTmpTable());
    }
}
