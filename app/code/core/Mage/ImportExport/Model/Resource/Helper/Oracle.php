<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Sales
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
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
            // Sequence is not inited - we need to call NEXTVAL first
            $row = $adapter->fetchRow("SELECT {$seqName}.NEXTVAL AS current_id FROM dual");
        }
        return $row['current_id'] + 1;
    }
}
