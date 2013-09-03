<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_VersionsCms
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Hierarchy Lock Resource Model
 *
 * @category    Magento
 * @package     Magento_VersionsCms
 * @author      Magento Core Team <core@magentocommerce.com>
 */

/**
 * @deprecated since 1.12.0.0
 */
class Magento_VersionsCms_Model_Resource_Hierarchy_Lock extends Magento_Core_Model_Resource_Db_Abstract
{
    /**
     * Initialize connection and define main table and field
     *
     */
    protected function _construct()
    {
        $this->_init('magento_versionscms_hierarchy_lock', 'lock_id');
    }

    /**
     * Return last lock information
     *
     * @return array
     */
    public function getLockData()
    {
        $select = $this->_getReadAdapter()->select()
            ->from($this->getMainTable())
            ->order('lock_id ' . \Magento\DB\Select::SQL_DESC)
            ->limit(1);
        $data = $this->_getReadAdapter()->fetchRow($select);
        return is_array($data) ? $data : array();
    }
}
