<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Log
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Resource helper for specific requests to Oracle DB
 *
 * @category    Mage
 * @package     Mage_Log
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Log_Model_Resource_Helper_Oracle extends Mage_Core_Model_Resource_Helper_Oracle
{
    /**
     * Returns information about table in DB
     *
     * @param string $table
     * @return array
     */
    public function getTableInfo($table)
    {
        $adapter = $this->_getReadAdapter();
        if (!$adapter->isTableExists($table)) {
            return array();
        }

        $this->_computeTableStats($table);

        $result = array(
            'name' => $adapter->getTableName($table),
            'rows' => $this->_getTableNumRows($table),
            'data_length' => $this->_getTableSize($table),
            'index_length' => $this->_getTableIndexSize($table)
        );

        return $result;
    }

    /**
     * Returns number table rows or NULL, if table doesn't exist.
     *
     * @param string $table
     * @return Mage_Log_Model_Resource_Helper_Oracle
     */
    protected function _computeTableStats($table)
    {
        $adapter = $this->_getReadAdapter();
        $tableName = $adapter->getTableName($table);

        $query = $adapter->quoteInto('analyze table ' .  $tableName . ' compute statistics', $tableName);
        $adapter->query($query);

        return $this;
    }

    /**
     * Returns number table rows or NULL, if table doesn't exist.
     *
     * @param string $table
     * @return int|null
     */
    protected function _getTableNumRows($table)
    {
        $adapter = $this->_getReadAdapter();
        $tableName = $adapter->getTableName($table);

        $select = $adapter->select();
        $select->from('user_tables', array('num_rows'))
            ->where('table_name = ?', strtoupper($tableName));
        $row = $adapter->fetchRow($select);

        if (!$row) {
            return null;
        }
        return (int) $row['num_rows'];
    }

    /**
     * Returns table size on a disk
     *
     * @param string $table
     * @return int
     */
    protected function _getTableSize($table)
    {
        $adapter = $this->_getReadAdapter();
        $tableName = $adapter->getTableName($table);

        $select = $adapter->select();
        $select->from('user_extents', array('value' => 'SUM(bytes)'))
            ->where("segment_type = 'TABLE'")
            ->where('segment_name = ?', strtoupper($tableName))
            ->group('segment_name');
        $row = $adapter->fetchRow($select);

        return $row ? $row['value'] : 0;
    }

    /**
     * Returns table indexes size
     *
     * @param string $table
     * @return int
     */
    protected function _getTableIndexSize($table)
    {
        $tableIndexes = $this->_getTableIndexes($table);
        if (!$tableIndexes) {
            return 0;
        }

        $adapter = $this->_getReadAdapter();
        $select = $adapter->select();
        $select->from('user_extents', array('value' => 'SUM(bytes)'))
            ->where("segment_type = 'INDEX'")
            ->where('segment_name IN(?)', $tableIndexes)
            ->group('segment_name');
        $row = $adapter->fetchRow($select);

        return $row ? $row['value'] : 0;
    }

    /**
     * Returns table indexes size
     *
     * @param string $table
     * @return int
     */
    protected function _getTableIndexes($table)
    {
        $adapter = $this->_getReadAdapter();
        $tableName = $adapter->getTableName($table);
        $select = $adapter->select();
        $select->from('user_indexes', 'index_name')
            ->where('table_name = ?', strtoupper($tableName));
        return $adapter->fetchAll($select);
    }
}
