<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Log
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Resource helper for specific requests to MySQL DB
 *
 * @category    Magento
 * @package     Magento_Log
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Log\Model\Resource\Helper;

class Mysql4 extends \Magento\Core\Model\Resource\Helper\Mysql4
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

        $query = $adapter->quoteInto('SHOW TABLE STATUS LIKE ?', $tableName);
        $status = $adapter->fetchRow($query);
        if (!$status) {
            return array();
        }

        return array(
            'name' => $tableName,
            'rows' => $status['Rows'],
            'data_length' => $status['Data_length'],
            'index_length' => $status['Index_length']
        );
    }
}
