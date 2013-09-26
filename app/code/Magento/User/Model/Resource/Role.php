<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Admin role resource model
 */
class Magento_User_Model_Resource_Role extends Magento_Core_Model_Resource_Db_Abstract
{
    /**
     * Users table
     *
     * @var string
     */
    protected $_usersTable;

    /**
     * Rule table
     *
     * @var string
     */
    protected $_ruleTable;

    /**
     * Cache
     *
     * @var Magento_Cache_FrontendInterface
     */
    protected $_cache;

    /**
     * Construct
     *
     * @param Magento_Core_Model_Resource $resource
     * @param Magento_Core_Model_CacheInterface $cache
     */
    public function __construct(
        Magento_Core_Model_Resource $resource,
        Magento_Core_Model_CacheInterface $cache
    ) {
        parent::__construct($resource);
        $this->_cache = $cache->getFrontend();
    }

    /**
     * Define main table
     *
     */
    protected function _construct()
    {
        $this->_init('admin_role', 'role_id');

        $this->_usersTable = $this->getTable('admin_user');
        $this->_ruleTable = $this->getTable('admin_rule');
    }

    /**
     * Process role before saving
     *
     * @param Magento_Core_Model_Abstract $role
     * @return Magento_User_Model_Resource_Role
     */
    protected function _beforeSave(Magento_Core_Model_Abstract $role)
    {
        if (!$role->getId()) {
            $role->setCreated($this->formatDate(true));
        }
        $role->setModified($this->formatDate(true));

        if ($role->getId() == '') {
            if ($role->getIdFieldName()) {
                $role->unsetData($role->getIdFieldName());
            } else {
                $role->unsetData('id');
            }
        }

        if (!$role->getTreeLevel()) {
            if ($role->getPid() > 0) {
                $select = $this->_getReadAdapter()->select()
                    ->from($this->getMainTable(), array('tree_level'))
                    ->where("{$this->getIdFieldName()} = :pid");

                $binds = array(
                    'pid' => (int) $role->getPid(),
                );

                $treeLevel = $this->_getReadAdapter()->fetchOne($select, $binds);
            } else {
                $treeLevel = 0;
            }

            $role->setTreeLevel($treeLevel + 1);
        }

        if ($role->getName()) {
            $role->setRoleName($role->getName());
        }

        return $this;
    }

    /**
     * Process role after saving
     *
     * @param Magento_Core_Model_Abstract $role
     * @return Magento_User_Model_Resource_Role
     */
    protected function _afterSave(Magento_Core_Model_Abstract $role)
    {
        $this->_updateRoleUsersAcl($role);
        $this->_cache->clean(Zend_Cache::CLEANING_MODE_MATCHING_TAG,
            array(Magento_Backend_Block_Menu::CACHE_TAGS));
        return $this;
    }

    /**
     * Process role after deleting
     *
     * @param Magento_Core_Model_Abstract $role
     * @return Magento_User_Model_Resource_Role
     */
    protected function _afterDelete(Magento_Core_Model_Abstract $role)
    {
        $adapter = $this->_getWriteAdapter();

        $adapter->delete(
            $this->getMainTable(),
            array('parent_id = ?' => (int) $role->getId())
        );

        $adapter->delete(
            $this->_ruleTable,
            array('role_id = ?' => (int) $role->getId())
        );

        return $this;
    }

    /**
     * Get role users
     *
     * @param Magento_User_Model_Role $role
     * @return array
     */
    public function getRoleUsers(Magento_User_Model_Role $role)
    {
        $read = $this->_getReadAdapter();

        $binds = array(
            'role_id'   => $role->getId(),
            'role_type' => 'U'
        );

        $select = $read->select()
            ->from($this->getMainTable(), array('user_id'))
            ->where('parent_id = :role_id')
            ->where('role_type = :role_type')
            ->where('user_id > 0');

        return $read->fetchCol($select, $binds);
    }

    /**
     * Update role users ACL
     *
     * @param Magento_User_Model_Role $role
     * @return bool
     */
    private function _updateRoleUsersAcl(Magento_User_Model_Role $role)
    {
        $write  = $this->_getWriteAdapter();
        $users  = $this->getRoleUsers($role);
        $rowsCount = 0;

        if (sizeof($users) > 0) {
            $bind  = array('reload_acl_flag' => 1);
            $where = array('user_id IN(?)' => $users);
            $rowsCount = $write->update($this->_usersTable, $bind, $where);
        }

        return $rowsCount > 0;
    }
}
