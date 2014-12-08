<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\VersionsCms\Model\Resource\Hierarchy;

/**
 * Hierarchy Lock Resource Model
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */

/**
 * @deprecated since 1.12.0.0
 */
class Lock extends \Magento\Framework\Model\Resource\Db\AbstractDb
{
    /**
     * Initialize connection and define main table and field
     *
     * @return void
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
        $select = $this->_getReadAdapter()->select()->from(
            $this->getMainTable()
        )->order(
            'lock_id ' . \Magento\Framework\DB\Select::SQL_DESC
        )->limit(
            1
        );
        $data = $this->_getReadAdapter()->fetchRow($select);
        return is_array($data) ? $data : [];
    }
}
