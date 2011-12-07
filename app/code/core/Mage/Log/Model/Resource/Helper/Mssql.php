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
 * Resource helper for specific requests to SQL Server DB
 *
 * @category    Mage
 * @package     Mage_Log
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Log_Model_Resource_Helper_Mssql extends Mage_Core_Model_Resource_Helper_Mssql
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
        $tableName = $adapter->getTableName($table);
        if (!$adapter->isTableExists($tableName)) {
            return array();
        }

        $query = $adapter->quoteInto('EXEC sp_spaceused ?', $tableName);
        $status = $adapter->fetchRow($query);
        if (!$status) {
            return array();
        }

        return array(
            'name' => $tableName,
            'rows' => $status['rows'],
            'data_length' => $this->_humanReadable2Bytes($status['data']),
            'index_length' => $this->_humanReadable2Bytes($status['index_size'])
        );
    }

    /**
     * Converts human readable value, returned by DB, to number of bytes
     *
     * @param string $string
     * @return int
     */
    protected function _humanReadable2Bytes($string)
    {
        $result = (int) $string;
        $scales = array(
            'kb' => 1024,
            'mb' => 1024 * 1024,
            'gb' => 1024 * 1024 * 1024
        );
        foreach ($scales as $needle => $factor) {
            if (stripos($string, $needle) !== false) {
                $result *= $factor;
                break;
            }
        }
        return $result;
    }
}
