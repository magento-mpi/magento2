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
     * @param \Magento\DB\Adapter\AdapterInterface $connection
     */
    public function __construct(
        \Magento\DB\Adapter\AdapterInterface $connection
    ) {
        $this->_connection = $connection;
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
        $renameTables = array();

        if ($this->_connection->isTableExists($flatTable)) {
            $renameTables[] = array(
                'oldName' => $flatTable,
                'newName' => $flatDropName,
            );
        }
        $renameTables[] = array(
            'oldName' => $temporaryFlatTableName,
            'newName' => $flatTable,
        );

        $this->_connection->dropTable($flatDropName);
        $this->_connection->renameTablesBatch($renameTables);
        $this->_connection->dropTable($flatDropName);
    }
}
