<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Cms
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Hierarchy Lock Resource Model
 *
 * @category    Enterprise
 * @package     Enterprise_Cms
 * @author      Magento Core Team <core@magentocommerce.com>
 */

/**
 * @deprecated since 1.12.0.0
 */
class Enterprise_Cms_Model_Resource_Hierarchy_Lock extends Magento_Core_Model_Resource_Db_Abstract
{
    /**
     * Initialize connection and define main table and field
     *
     */
    protected function _construct()
    {
        $this->_init('enterprise_cms_hierarchy_lock', 'lock_id');
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
            ->order('lock_id ' . Magento_DB_Select::SQL_DESC)
            ->limit(1);
        $data = $this->_getReadAdapter()->fetchRow($select);
        return is_array($data) ? $data : array();
    }
}
