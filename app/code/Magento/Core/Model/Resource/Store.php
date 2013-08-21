<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Core Store Resource Model
 *
 * @category    Magento
 * @package     Magento_Core
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Core_Model_Resource_Store extends Magento_Core_Model_Resource_Db_Abstract
{
    /**
     * Define main table and primary key
     *
     */
    protected function _construct()
    {
        $this->_init('core_store', 'store_id');
    }

    /**
     * Count number of all entities in the system
     *
     * By default won't count admin store
     *
     * @param bool $countAdmin
     * @return int
     */
    public function countAll($countAdmin = false)
    {
        $adapter = $this->_getReadAdapter();
        $select = $adapter->select()->from($this->getMainTable(), 'COUNT(*)');
        if (!$countAdmin) {
            $select->where(sprintf('%s <> %s', $adapter->quoteIdentifier('code'), $adapter->quote('admin')));
        }
        return (int)$adapter->fetchOne($select);
    }

    /**
     * Initialize unique fields
     *
     * @return Magento_Core_Model_Resource_Store
     */
    protected function _initUniqueFields()
    {
        $this->_uniqueFields = array(array(
            'field' => 'code',
            'title' => __('Store with the same code')
        ));
        return $this;
    }

    /**
     * Update Store Group data after save store
     *
     * @param Magento_Core_Model_Abstract $object
     * @return Magento_Core_Model_Resource_Store
     */
    protected function _afterSave(Magento_Core_Model_Abstract $object)
    {
        parent::_afterSave($object);
        $this->_updateGroupDefaultStore($object->getGroupId(), $object->getId());
        $this->_changeGroup($object);

        return $this;
    }

    /**
     * Remove core configuration data after delete store
     *
     * @param Magento_Core_Model_Abstract $model
     * @return Magento_Core_Model_Resource_Store
     */
    protected function _afterDelete(Magento_Core_Model_Abstract $model)
    {
        $where = array(
            'scope = ?'    => Magento_Core_Model_Config::SCOPE_STORES,
            'scope_id = ?' => $model->getStoreId()
        );

        $this->_getWriteAdapter()->delete(
            $this->getTable('core_config_data'),
            $where
        );
        return $this;
    }

    /**
     * Update Default store for Store Group
     *
     * @param int $groupId
     * @param int $storeId
     * @return Magento_Core_Model_Resource_Store
     */
    protected function _updateGroupDefaultStore($groupId, $storeId)
    {
        $adapter    = $this->_getWriteAdapter();

        $bindValues = array('group_id' => (int)$groupId);
        $select = $adapter->select()
            ->from($this->getMainTable(), array('count' => 'COUNT(*)'))
            ->where('group_id = :group_id');
        $count  = $adapter->fetchOne($select, $bindValues);

        if ($count == 1) {
            $bind  = array('default_store_id' => (int)$storeId);
            $where = array('group_id = ?' => (int)$groupId);
            $adapter->update($this->getTable('core_store_group'), $bind, $where);
        }

        return $this;
    }

    /**
     * Change store group for store
     *
     * @param Magento_Core_Model_Abstract $model
     * @return Magento_Core_Model_Resource_Store
     */
    protected function _changeGroup(Magento_Core_Model_Abstract $model)
    {
        if ($model->getOriginalGroupId() && $model->getGroupId() != $model->getOriginalGroupId()) {
            $adapter = $this->_getReadAdapter();
            $select = $adapter->select()
                ->from($this->getTable('core_store_group'), 'default_store_id')
                ->where($adapter->quoteInto('group_id=?', $model->getOriginalGroupId()));
            $storeId = $adapter->fetchOne($select, 'default_store_id');

            if ($storeId == $model->getId()) {
                $bind = array('default_store_id' => Magento_Core_Model_AppInterface::ADMIN_STORE_ID);
                $where = array('group_id = ?' => $model->getOriginalGroupId());
                $this->_getWriteAdapter()->update($this->getTable('core_store_group'), $bind, $where);
            }
        }
        return $this;
    }

    /**
     * Retrieve select object for load object data
     *
     * @param string $field
     * @param mixed $value
     * @param Magento_Core_Model_Abstract $object
     * @return Magento_DB_Select
     */
    protected function _getLoadSelect($field, $value, $object)
    {
        $select = parent::_getLoadSelect($field, $value, $object);
        $select->order('sort_order');
        return $select;
    }
}
