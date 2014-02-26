<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Model\Indexer\Product\Flat\Action\Rows;

/**
 * Class TableData
 * @package Magento\Catalog\Model\Indexer\Product\Flat\Action\Rows
 */
class TableData implements \Magento\Catalog\Model\Indexer\Product\Flat\TableDataInterface
{
    /**
     * @var \Magento\DB\Adapter\AdapterInterface
     */
    protected $_connection;

    /**
     * @var \Magento\Catalog\Helper\Product\Flat\Indexer
     */
    protected $_productIndexerHelper;

    /**
     * @param \Magento\DB\Adapter\AdapterInterface $connection
     * @param \Magento\Catalog\Helper\Product\Flat\Indexer $productIndexerHelper
     */
    public function __construct(
        \Magento\DB\Adapter\AdapterInterface $connection,
        \Magento\Catalog\Helper\Product\Flat\Indexer $productIndexerHelper
    ) {
        $this->_connection = $connection;
        $this->_productIndexerHelper = $productIndexerHelper;
    }

    /**
     * Move data from temporary tables to flat
     *
     * @param string $flatTable
     * @param string $flatDropName
     * @param string $temporaryFlatTableName
     */
    public function move($flatTable, $flatDropName, $temporaryFlatTableName)
    {
        if (!$this->_connection->isTableExists($flatTable)) {
            $this->_connection->dropTable($flatDropName);
            $this->_connection->renameTablesBatch(array(
                'oldName' => $temporaryFlatTableName,
                'newName' => $flatTable
            ));
            $this->_connection->dropTable($flatDropName);
        } else {
            $describe = $this->_connection->describeTable($flatTable);
            $columns  = $this->_productIndexerHelper->getFlatColumns();
            $columns  = array_keys(array_intersect_key($describe, $columns));
            $select   = $this->_connection->select();

            $select->from(
                array('tf' => sprintf('%s_tmp_indexer', $flatTable)),
                $columns
            );
            $sql = $select->insertFromSelect($flatTable, $columns);
            $this->_connection->query($sql);

            $this->_connection->dropTable(
                sprintf('%s_tmp_indexer', $flatTable)
            );
        }
    }
}
