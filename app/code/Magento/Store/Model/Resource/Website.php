<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Store\Model\Resource;

/**
 * Website Resource Model
 */
class Website extends \Magento\Model\Resource\Db\AbstractDb
{
    /**
     * Define main table
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('store_website', 'website_id');
    }

    /**
     * Initialize unique fields
     *
     * @return $this
     */
    protected function _initUniqueFields()
    {
        $this->_uniqueFields = array(array('field' => 'code', 'title' => __('Website with the same code')));
        return $this;
    }

    /**
     * Validate website code before object save
     *
     * @param \Magento\Model\AbstractModel $object
     * @return $this
     * @throws \Magento\Model\Exception
     */
    protected function _beforeSave(\Magento\Model\AbstractModel $object)
    {
        if (!preg_match('/^[a-z]+[a-z0-9_]*$/', $object->getCode())) {
            throw new \Magento\Model\Exception(
                __(
                    'Website code may only contain letters (a-z), numbers (0-9) or underscore(_), the first character must be a letter'
                )
            );
        }

        return parent::_beforeSave($object);
    }

    /**
     * Perform actions after object save
     *
     * @param \Magento\Model\AbstractModel $object
     * @return $this
     */
    protected function _afterSave(\Magento\Model\AbstractModel $object)
    {
        if ($object->getIsDefault()) {
            $this->_getWriteAdapter()->update($this->getMainTable(), array('is_default' => 0));
            $where = array('website_id = ?' => $object->getId());
            $this->_getWriteAdapter()->update($this->getMainTable(), array('is_default' => 1), $where);
        }
        return parent::_afterSave($object);
    }

    /**
     * Remove configuration data after delete website
     *
     * @param \Magento\Model\AbstractModel $model
     * @return $this
     */
    protected function _afterDelete(\Magento\Model\AbstractModel $model)
    {
        $where = array(
            'scope = ?' => \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITES,
            'scope_id = ?' => $model->getWebsiteId()
        );

        $this->_getWriteAdapter()->delete($this->getTable('core_config_data'), $where);

        return $this;
    }

    /**
     * Retrieve default stores select object
     * Select fields website_id, store_id
     *
     * @param bool $includeDefault include/exclude default admin website
     * @return \Magento\DB\Select
     */
    public function getDefaultStoresSelect($includeDefault = false)
    {
        $ifNull = $this->_getReadAdapter()->getCheckSql(
            'store_group_table.default_store_id IS NULL',
            '0',
            'store_group_table.default_store_id'
        );
        $select = $this->_getReadAdapter()->select()->from(
            array('website_table' => $this->getTable('store_website')),
            array('website_id')
        )->joinLeft(
            array('store_group_table' => $this->getTable('store_group')),
            'website_table.website_id=store_group_table.website_id' .
            ' AND website_table.default_group_id = store_group_table.group_id',
            array('store_id' => $ifNull)
        );
        if (!$includeDefault) {
            $select->where('website_table.website_id <> ?', 0);
        }
        return $select;
    }

    /**
     * Get total number of persistent entities in the system, excluding the admin website by default
     *
     * @param bool $includeDefault
     * @return int
     */
    public function countAll($includeDefault = false)
    {
        $adapter = $this->_getReadAdapter();
        $select = $adapter->select()->from($this->getMainTable(), 'COUNT(*)');
        if (!$includeDefault) {
            $select->where('website_id <> ?', 0);
        }
        return (int)$adapter->fetchOne($select);
    }
}
