<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Model\Indexer\Product\Flat;

/**
 * Class TableData
 * @package Magento\Catalog\Model\Indexer\Product\Flat
 */
class TableData implements TableDataInterface
{
    /**
     * @var \Magento\DB\Adapter\AdapterInterface
     */
    protected $_connection;

    /**
     * @var \Magento\App\Resource
     */
    protected $_resource;

    /**
     * @param \Magento\App\Resource $resource
     */
    public function __construct(
        \Magento\App\Resource $resource
    ) {
        $this->_resource = $resource;
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
        $renameTables = array();

        if ($connection->isTableExists($flatTable)) {
            $renameTables[] = array(
                'oldName' => $flatTable,
                'newName' => $flatDropName,
            );
        }
        $renameTables[] = array(
            'oldName' => $temporaryFlatTableName,
            'newName' => $flatTable,
        );

        $connection->dropTable($flatDropName);
        $connection->renameTablesBatch($renameTables);
        $connection->dropTable($flatDropName);
    }
}
