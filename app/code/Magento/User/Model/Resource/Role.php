<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\User\Model\Resource;

use Magento\User\Model\Acl\Role\User as RoleUser;

/**
 * Admin role resource model
 */
class Role extends \Magento\Framework\Model\Resource\Db\AbstractDb
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
     * @var \Magento\Cache\FrontendInterface
     */
    protected $_cache;

    /**
     * @var \Magento\Stdlib\DateTime
     */
    protected $dateTime;

    /**
     * @param \Magento\Framework\App\Resource $resource
     * @param \Magento\Framework\App\CacheInterface $cache
     * @param \Magento\Stdlib\DateTime $dateTime
     */
    public function __construct(
        \Magento\Framework\App\Resource $resource,
        \Magento\Framework\App\CacheInterface $cache,
        \Magento\Stdlib\DateTime $dateTime
    ) {
        $this->dateTime = $dateTime;
        parent::__construct($resource);
        $this->_cache = $cache->getFrontend();
    }

    /**
     * Define main table
     *
     * @return void
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
     * @param \Magento\Framework\Model\AbstractModel $role
     * @return $this
     */
    protected function _beforeSave(\Magento\Framework\Model\AbstractModel $role)
    {
        if (!$role->getId()) {
            $role->setCreated($this->dateTime->formatDate(true));
        }
        $role->setModified($this->dateTime->formatDate(true));

        if ($role->getId() == '') {
            if ($role->getIdFieldName()) {
                $role->unsetData($role->getIdFieldName());
            } else {
                $role->unsetData('id');
            }
        }

        if (!$role->getTreeLevel()) {
            if ($role->getPid() > 0) {
                $select = $this->_getReadAdapter()->select()->from(
                    $this->getMainTable(),
                    array('tree_level')
                )->where(
                    "{$this->getIdFieldName()} = :pid"
                );

                $binds = array('pid' => (int)$role->getPid());

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
     * @param \Magento\Framework\Model\AbstractModel $role
     * @return $this
     */
    protected function _afterSave(\Magento\Framework\Model\AbstractModel $role)
    {
        $this->_updateRoleUsersAcl($role);
        $this->_cache->clean(\Zend_Cache::CLEANING_MODE_MATCHING_TAG, array(\Magento\Backend\Block\Menu::CACHE_TAGS));
        return $this;
    }

    /**
     * Process role after deleting
     *
     * @param \Magento\Framework\Model\AbstractModel $role
     * @return $this
     */
    protected function _afterDelete(\Magento\Framework\Model\AbstractModel $role)
    {
        $adapter = $this->_getWriteAdapter();

        $adapter->delete($this->getMainTable(), array('parent_id = ?' => (int)$role->getId()));

        $adapter->delete($this->_ruleTable, array('role_id = ?' => (int)$role->getId()));

        return $this;
    }

    /**
     * Get role users
     *
     * @param \Magento\User\Model\Role $role
     * @return array
     */
    public function getRoleUsers(\Magento\User\Model\Role $role)
    {
        $read = $this->_getReadAdapter();

        $binds = array('role_id' => $role->getId(), 'role_type' => RoleUser::ROLE_TYPE);

        $select = $read->select()->from(
            $this->getMainTable(),
            array('user_id')
        )->where(
            'parent_id = :role_id'
        )->where(
            'role_type = :role_type'
        )->where(
            'user_id > 0'
        );

        return $read->fetchCol($select, $binds);
    }

    /**
     * Update role users ACL
     *
     * @param \Magento\User\Model\Role $role
     * @return bool
     */
    private function _updateRoleUsersAcl(\Magento\User\Model\Role $role)
    {
        $write = $this->_getWriteAdapter();
        $users = $this->getRoleUsers($role);
        $rowsCount = 0;

        if (sizeof($users) > 0) {
            $bind = array('reload_acl_flag' => 1);
            $where = array('user_id IN(?)' => $users);
            $rowsCount = $write->update($this->_usersTable, $bind, $where);
        }

        return $rowsCount > 0;
    }
}
