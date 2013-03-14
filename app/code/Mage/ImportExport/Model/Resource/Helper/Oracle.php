<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * ImportExport Oracle resource helper model
 *
 * @category    Mage
 * @package     Mage_Sales
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_ImportExport_Model_Resource_Helper_Oracle extends Mage_Core_Model_Resource_Helper_Oracle
{
    /**
     * Constants to be used for DB
     */
    const DB_MAX_STATEMENT_SIZE = 65536; // Maximal statement length in Oracle

    /**
     * Returns maximum size of packet, that we can send to DB
     *
     * @return int
     */
    public function getMaxDataSize()
    {
        return self::DB_MAX_STATEMENT_SIZE;
    }

    /**
     * Returns next autoincrement value for a table.
     *
     * @param string $table Real table name in DB
     * @return int
     */
    public function getNextAutoincrement($table)
    {
        $adapter = $this->_getReadAdapter();
        $seqName = $adapter->getSequenceName($table);
        try {
            $row = $adapter->fetchRow("SELECT {$seqName}.CURRVAL AS current_id FROM dual");
        } catch (Exception $e) {
            // Maybe sequence is not inited - and we need to call NEXTVAL first
            if (strpos($e->getMessage(), 'ORA-08002') !== false) {
                $row = $adapter->fetchRow("SELECT {$seqName}.NEXTVAL AS current_id FROM dual");
            } else {
                throw $e;
            }
        }
        return $row['current_id'] + 1;
    }
}
