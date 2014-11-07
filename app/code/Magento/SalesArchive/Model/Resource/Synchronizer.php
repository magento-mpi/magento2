<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\SalesArchive\Model\Resource;

/**
 * Module synchronizer
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Synchronizer
{
    /**
     * Connection Instance
     *
     * @var \Magento\Framework\Module\Setup
     */
    protected $_installer;

    /**
     * Map of tables aliases to archive tables
     *
     * @var array
     */
    protected $_tablesMap = array(
        'sales_order_grid' => 'magento_sales_order_grid_archive',
        'sales_invoice_grid' => 'magento_sales_invoice_grid_archive',
        'sales_creditmemo_grid' => 'magento_sales_creditmemo_grid_archive',
        'sales_shipment_grid' => 'magento_sales_shipment_grid_archive'
    );

    /**
     * Map of flat tables to archive tables
     *
     * @var array
     */
    protected $_tableContraintMap = array(
        'sales_order_grid' => array('SALES_ORDER_GRID', 'SALES_ORDER_GRID_ARCHIVE'),
        'sales_invoice_grid' => array('SALES_INVOICE_GRID', 'SALES_INVOICE_GRID_ARCHIVE'),
        'sales_creditmemo_grid' => array('SALES_CREDITMEMO_GRID', 'SALES_CREDITMEMO_GRID_ARCHIVE'),
        'sales_shipment_grid' => array('SALES_SHIPMENT_GRID', 'SALES_SHIPMENT_GRID_ARCHIVE')
    );

    /**
     * Default Constructor
     *
     * @param \Magento\Framework\Module\Setup|\Magento\Setup\Module\Updater\SetupInterface $installer
     */
    public function __construct($installer)
    {
        $this->_installer = $installer;
    }

    /**
     * Synchronize archive structure
     *
     * @return $this
     */
    public function syncArchiveStructure()
    {
        foreach ($this->_tablesMap as $sourceTable => $targetTable) {
            $this->_syncTable(
                $this->_installer->getTable($sourceTable),
                $this->_installer->getTable($targetTable)
            );
        }
        return $this;
    }

    /**
     * Fast table describe retrieve
     *
     * @param string $table
     * @return array
     */
    protected function _fastDescribe($table)
    {
        $description = $this->_installer->getConnection()->describeTable($table);
        $result = array();
        foreach ($description as $column) {
            $result[$column['COLUMN_NAME']] = $column['DATA_TYPE'];
        }
        return $result;
    }

    /**
     * Synchronize tables structure
     *
     * @param string $sourceTable
     * @param string $targetTable
     * @return $this
     */
    protected function _syncTable($sourceTable, $targetTable)
    {
        $adapter = $this->_installer->getConnection();
        if (!$this->_installer->tableExists($targetTable)) {
            $newTable = $adapter->createTableByDdl($sourceTable, $targetTable);
            $adapter->createTable($newTable);
        } else {
            $sourceFields = $adapter->describeTable($sourceTable);
            $targetFields = $adapter->describeTable($targetTable);
            foreach ($sourceFields as $field => $definition) {
                if (isset($targetFields[$field])) {
                    if ($this->_checkColumnDifference($targetFields[$field], $definition)) {
                        $adapter->modifyColumnByDdl($targetTable, $field, $definition);
                    }
                } else {
                    $columnInfo = $adapter->getColumnCreateByDescribe($definition);
                    $adapter->addColumn($targetTable, $field, $columnInfo);
                    $targetFields[$field] = $definition;
                }
            }

            $previous = false;
            // Synchronize column positions
            $sourceFields = $this->_fastDescribe($sourceTable);
            $targetFields = $this->_fastDescribe($targetTable);
            foreach ($sourceFields as $field => $definition) {
                if ($previous === false) {
                    reset($targetFields);
                    if (key($targetFields) !== $field) {
                        $this->changeColumnPosition($targetTable, $field, false, true);
                    }
                } else {
                    reset($targetFields);
                    $currentKey = key($targetFields);
                    // Search for column position in target table
                    while ($currentKey !== $field) {
                        if (next($targetFields) === false) {
                            $currentKey = false;
                            break;
                        }
                        $currentKey = key($targetFields);
                    }
                    if ($currentKey) {
                        $moved = prev($targetFields) !== false;
                        // If column positions is different
                        if ($moved && $previous !== key($targetFields) || !$moved) {
                            $this->changeColumnPosition($targetTable, $field, $previous);
                        }
                    }
                }
                $previous = $field;
            }
            $this->_syncTableIndex($sourceTable, $targetTable);

            if (isset($this->_tableContraintMap[$sourceTable])) {
                $this->_syncTableConstraint(
                    $sourceTable,
                    $targetTable,
                    $this->_tableContraintMap[$sourceTable][0],
                    $this->_tableContraintMap[$sourceTable][1]
                );
            }
        }

        return $this;
    }

    /**
     * Change columns position
     *
     * @param string $table
     * @param string $column
     * @param bool $after
     * @param bool $first
     * @return $this
     */
    public function changeColumnPosition($table, $column, $after = false, $first = false)
    {
        $this->_changeColumnPosition($table, $column, $after, $first);
        return $this;
    }

    /**
     * Syncronize table indexes
     *
     * @param string $sourceTable
     * @param string $targetTable
     * @return $this
     */
    protected function _syncTableIndex($sourceTable, $targetTable)
    {
        $sourceIndex = $this->_installer->getConnection()->getIndexList($sourceTable);
        $targetIndex = $this->_installer->getConnection()->getIndexList($targetTable);
        foreach ($sourceIndex as $indexKey => $indexData) {
            $indexExists = false;
            foreach ($targetIndex as $targetIndexKey => $targetIndexData) {
                if (!$this->_checkIndexDifference($indexData, $targetIndexData)) {
                    $indexExists = true;
                    break;
                }
            }
            if (!$indexExists) {
                $newIndexName = $this->_installer->getConnection()->getIndexName(
                    $targetTable,
                    $indexData['COLUMNS_LIST'],
                    $indexData['INDEX_TYPE']
                );
                $this->_installer->getConnection()->addIndex(
                    $targetTable,
                    $newIndexName,
                    $indexData['COLUMNS_LIST'],
                    $indexData['INDEX_TYPE']
                );
            }
        }

        return $this;
    }

    /**
     * Check column difference for synchronization
     *
     * @param array $sourceColumn
     * @param array $targetColumn
     * @return bool
     */
    protected function _checkColumnDifference($sourceColumn, $targetColumn)
    {
        unset($sourceColumn['TABLE_NAME']);
        unset($targetColumn['TABLE_NAME']);

        return $sourceColumn !== $targetColumn;
    }

    /**
     * Check indicies difference for synchronization
     *
     * @param array $sourceIndex
     * @param array $targetIndex
     * @return bool
     */
    protected function _checkIndexDifference($sourceIndex, $targetIndex)
    {
        return strtoupper(
            $sourceIndex['INDEX_TYPE']
        ) != strtoupper(
            $targetIndex['INDEX_TYPE']
        ) || count(
            array_diff($sourceIndex['COLUMNS_LIST'], $targetIndex['COLUMNS_LIST'])
        ) > 0;
    }

    /**
     * Check indexes difference for synchronization
     *
     * @param array $sourceConstraint
     * @param array $targetConstraint
     * @return bool
     */
    protected function _checkConstraintDifference($sourceConstraint, $targetConstraint)
    {
        return $sourceConstraint['COLUMN_NAME'] != $targetConstraint['COLUMN_NAME'] ||
            $sourceConstraint['REF_TABLE_NAME'] != $targetConstraint['REF_TABLE_NAME'] ||
            $sourceConstraint['REF_COLUMN_NAME'] != $targetConstraint['REF_COLUMN_NAME'] ||
            $sourceConstraint['ON_DELETE'] != $targetConstraint['ON_DELETE'] ||
            $sourceConstraint['ON_UPDATE'] != $targetConstraint['ON_UPDATE'];
    }

    /**
     * Synchronize tables foreign keys
     *
     * @param string $sourceTable
     * @param string $targetTable
     * @param string $sourceKey
     * @param string $targetKey
     * @return $this
     */
    protected function _syncTableConstraint($sourceTable, $targetTable, $sourceKey, $targetKey)
    {
        $sourceConstraints = $this->_installer->getConnection()->getForeignKeys($sourceTable);
        $targetConstraints = $this->_installer->getConnection()->getForeignKeys($targetTable);

        $targetConstraintUsedInSource = array();
        foreach ($sourceConstraints as $constraintInfo) {
            $targetConstraint = $this->_installer->getConnection()->getForeignKeyName(
                $targetTable,
                $constraintInfo['COLUMN_NAME'],
                $constraintInfo['REF_TABLE_NAME'],
                $constraintInfo['REF_COLUMN_NAME']
            );
            if (!isset(
                $targetConstraints[$targetConstraint]
            ) || $this->_checkConstraintDifference(
                $constraintInfo,
                $targetConstraints[$targetConstraint]
            )
            ) {
                $this->_installer->getConnection()->addForeignKey(
                    $targetConstraint,
                    $targetTable,
                    $constraintInfo['COLUMN_NAME'],
                    $constraintInfo['REF_TABLE_NAME'],
                    $constraintInfo['REF_COLUMN_NAME'],
                    $constraintInfo['ON_DELETE'],
                    $constraintInfo['ON_UPDATE']
                );
            }

            $targetConstraintUsedInSource[] = $targetConstraint;
        }

        $constraintToDelete = array_diff(array_keys($targetConstraints), $targetConstraintUsedInSource);

        foreach ($constraintToDelete as $constraint) {
            // Clear old not used constraints
            $this->_installer->getConnection()->dropForeignKey($targetTable, $constraint);
        }

        return $this;
    }

    /**
     * Change columns position
     *
     * @param string $table
     * @param string $column
     * @param bool $after
     * @param bool $first
     * @return $this
     * @throws \Exception
     */
    protected function _changeColumnPosition($table, $column, $after = false, $first = false)
    {
        if ($after && $first) {
            if (is_string($after)) {
                $first = false;
            } else {
                $after = false;
            }
        } elseif (!$after && !$first) {
            // If no new position specified
            return $this;
        }

        if (!$this->_installer->getConnection()->isTableExists($table)) {
            throw new \Exception(sprintf('Table `%s` not found!', $table));
        }

        $columns = array();
        $adapter = $this->_installer->getConnection();
        $description = $adapter->describeTable($table);
        foreach ($description as $columnDescription) {
            $columns[$columnDescription['COLUMN_NAME']] = $adapter->getColumnDefinitionFromDescribe(
                $columnDescription
            );
        }

        if (!isset($columns[$column])) {
            throw new \Exception(sprintf('Column `%s` not found in table `%s`!', $column, $table));
        } elseif ($after && !isset($columns[$after])) {
            throw new \Exception(sprintf('Positioning column `%s` not found in table `%s`!', $after, $table));
        }

        if ($after) {
            $sql = sprintf(
                'ALTER TABLE %s MODIFY COLUMN %s %s AFTER %s',
                $adapter->quoteIdentifier($table),
                $adapter->quoteIdentifier($column),
                $columns[$column],
                $adapter->quoteIdentifier($after)
            );
        } else {
            $sql = sprintf(
                'ALTER TABLE %s MODIFY COLUMN %s %s FIRST',
                $adapter->quoteIdentifier($table),
                $adapter->quoteIdentifier($column),
                $columns[$column]
            );
        }

        $adapter->query($sql);

        return $this;
    }
}
