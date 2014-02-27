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
     * @var \Magento\App\Resource
     */
    protected $_resource;

    /**
     * @param \Magento\App\Resource $resource
     * @param \Magento\Catalog\Helper\Product\Flat\Indexer $productIndexerHelper
     */
    public function __construct(
        \Magento\App\Resource $resource,
        \Magento\Catalog\Helper\Product\Flat\Indexer $productIndexerHelper
    ) {
        $this->_resource = $resource;
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
        $connection = $this->_resource->getConnection('write');
        if (!$connection->isTableExists($flatTable)) {
            $connection->dropTable($flatDropName);
            $connection->renameTablesBatch(array(
                'oldName' => $temporaryFlatTableName,
                'newName' => $flatTable
            ));
            $connection->dropTable($flatDropName);
        } else {
            $describe = $connection->describeTable($flatTable);
            $columns  = $this->_productIndexerHelper->getFlatColumns();
            $columns  = array_keys(array_intersect_key($describe, $columns));
            $select   = $connection->select();

            $select->from(
                array('tf' => sprintf('%s_tmp_indexer', $flatTable)),
                $columns
            );
            $sql = $select->insertFromSelect($flatTable, $columns);
            $connection->query($sql);

            $connection->dropTable(
                sprintf('%s_tmp_indexer', $flatTable)
            );
        }
    }
}
