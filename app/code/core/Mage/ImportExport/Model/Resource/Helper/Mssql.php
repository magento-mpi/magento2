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
 * ImportExport Sql Server resource helper model
 *
 * @category    Mage
 * @package     Mage_Sales
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_ImportExport_Model_Resource_Helper_Mssql extends Mage_Core_Model_Resource_Helper_Mssql
{
    /**
     * Constants to be used for DB
     */
    const DB_MAX_PACKET_SIZE        = 268435456; // Maximal packet length by default in Sql Server
    const DB_MAX_PACKET_COEFFICIENT = 0.9; // The coefficient of useful data from maximum packet length

    /**
     * Returns maximum size of packet, that we can send to DB
     *
     * @return int
     */
    public function getMaxDataSize()
    {
        return floor(self::DB_MAX_PACKET_SIZE * self::DB_MAX_PACKET_COEFFICIENT);
    }

    /**
     * Returns next autoincrement value for a table
     *
     * @param string $table Real table name in DB
     * @return int
     */
    public function getNextAutoincrement($table)
    {
        $adapter = $this->_getReadAdapter();
        $row = $adapter->fetchRow('SELECT IDENT_CURRENT(' . $adapter->quote($table) . ') AS current_id');
        return $row['current_id'] + 1;
    }
}
