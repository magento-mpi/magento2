<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * ACL user resource
 */
class Magento_User_Model_Resource_User extends Magento_Core_Model_Resource_Db_Abstract
{
    /**
     * @var Magento_Acl_CacheInterface
     */
    protected $_aclCache;

    /**
     * Role model
     *
     * @var Magento_User_Model_RoleFactory
     */
    protected $_roleFactory;

    /**
     * Construct
     *
     * @param Magento_Core_Model_Resource $resource
     * @param Magento_Acl_CacheInterface $aclCache
     * @param Magento_User_Model_RoleFactory $roleFactory
     */
    public function __construct(
        Magento_Core_Model_Resource $resource,
        Magento_Acl_CacheInterface $aclCache,
        Magento_User_Model_RoleFactory $roleFactory
    ) {
        parent::__construct($resource);
        $this->_aclCache = $aclCache;
        $this->_roleFactory = $roleFactory;
    }

    /**
     * Define main table
     *
     */
    protected function _construct()
    {
        $this->_init('admin_user', 'user_id');
    }

    /**
     * Initialize unique fields
     *
     * @return Magento_User_Model_Resource_User
     */
    protected function _initUniqueFields()
    {
        $this->_uniqueFields = array(
            array(
                'field' => 'email',
                'title' => __('Email')
            ),
            array(
                'field' => 'username',
                'title' => __('User Name')
            ),
        );
        return $this;
    }

    /**
     * Authenticate user by $username and $password
     *
     * @param Magento_User_Model_User $user
     * @return Magento_User_Model_Resource_User
     */
    public function recordLogin(Magento_User_Model_User $user)
    {
        $adapter = $this->_getWriteAdapter();

        $data = array(
            'logdate' => now(),
            'lognum'  => $user->getLognum() + 1
        );

        $condition = array(
            'user_id = ?' => (int) $user->getUserId(),
        );

        $adapter->update($this->getMainTable(), $data, $condition);

        return $this;
    }

    /**
     * Load data by specified username
     *
     * @param string $username
     * @return false|array
     */
    public function loadByUsername($username)
    {
        $adapter = $this->_getReadAdapter();

        $select = $adapter->select()
                    ->from($this->getMainTable())
                    ->where('username=:username');

        $binds = array(
            'username' => $username
        );

        return $adapter->fetchRow($select, $binds);
    }

    /**
     * Check if user is assigned to any role
     *
     * @param int|Magento_Core_Admin_Model_User $user
     * @return null|false|array
     */
    public function hasAssigned2Role($user)
    {
        if (is_numeric($user)) {
            $userId = $user;
        } else if ($user instanceof Magento_Core_Model_Abstract) {
            $userId = $user->getUserId();
        } else {
            return null;
        }

        if ( $userId > 0 ) {
            $adapter = $this->_getReadAdapter();

            $select = $adapter->select();
            $select->from($this->getTable('admin_role'))
                ->where('parent_id > :parent_id')
                ->where('user_id = :user_id');

            $binds = array(
                'parent_id' => 0,
                'user_id' => $userId,
            );

            return $adapter->fetchAll($select, $binds);
        } else {
            return null;
        }
    }

    /**
     * Set created/modified values before user save
     *
     * @param Magento_Core_Model_Abstract $user
     * @return Magento_User_Model_Resource_User
     */
    protected function _beforeSave(Magento_Core_Model_Abstract $user)
    {
        if ($user->isObjectNew()) {
            $user->setCreated($this->formatDate(true));
        }
        $user->setModified($this->formatDate(true));

        return parent::_beforeSave($user);
    }

    /**
     * Unserialize user extra data after user save
     *
     * @param Magento_Core_Model_Abstract $user
     * @return Magento_User_Model_Resource_User
     */
    protected function _afterSave(Magento_Core_Model_Abstract $user)
    {
        $user->setExtra(unserialize($user->getExtra()));
        if ($user->hasRoleId()) {
            $this->_clearUserRoles($user);
            $this->_createUserRole($user->getRoleId(), $user);
        }
        return $this;
    }

    /**
     * Clear all user-specific roles of provided user
     *
     * @param Magento_User_Model_User $user
     */
    public function _clearUserRoles(Magento_User_Model_User $user)
    {
        $conditions = array(
            'user_id = ?' => (int) $user->getId(),
        );
        $this->_getWriteAdapter()->delete($this->getTable('admin_role'), $conditions);
    }

    /**
     * Create role for provided user of provided type
     *
     * @param $parentId
     * @param Magento_User_Model_User $user
     */
    protected function _createUserRole($parentId, Magento_User_Model_User $user)
    {
        if ($parentId > 0) {
            /** @var Magento_User_Model_Role $parentRole */
            $parentRole = $this->_roleFactory->create()->load($parentId);
        } else {
            $role = new Magento_Object();
            $role->setTreeLevel(0);
        }

        if ($parentRole->getId()) {
            $data = new Magento_Object(array(
                'parent_id'  => $parentRole->getId(),
                'tree_level' => $parentRole->getTreeLevel() + 1,
                'sort_order' => 0,
                'role_type'  => 'U',
                'user_id'    => $user->getId(),
                'role_name'  => $user->getFirstname()
            ));

            $insertData = $this->_prepareDataForTable($data, $this->getTable('admin_role'));
            $this->_getWriteAdapter()->insert($this->getTable('admin_role'), $insertData);
            $this->_aclCache->clean();
        }
    }

    /**
     * Unserialize user extra data after user load
     *
     * @param Magento_Core_Model_Abstract $user
     * @return Magento_User_Model_Resource_User
     */
    protected function _afterLoad(Magento_Core_Model_Abstract $user)
    {
        if (is_string($user->getExtra())) {
            $user->setExtra(unserialize($user->getExtra()));
        }
        return parent::_afterLoad($user);
    }

    /**
     * Delete user role record with user
     *
     * @param Magento_Core_Model_Abstract $user
     * @return bool
     * @throws Magento_Core_Exception
     */
    public function delete(Magento_Core_Model_Abstract $user)
    {
        $this->_beforeDelete($user);
        $adapter = $this->_getWriteAdapter();

        $uid = $user->getId();
        $adapter->beginTransaction();
        try {
            $conditions = array(
                'user_id = ?' => $uid
            );

            $adapter->delete($this->getMainTable(), $conditions);
            $adapter->delete($this->getTable('admin_role'), $conditions);
        } catch (Magento_Core_Exception $e) {
            throw $e;
            return false;
        } catch (Exception $e){
            $adapter->rollBack();
            return false;
        }
        $adapter->commit();
        $this->_afterDelete($user);
        return true;
    }

    /**
     * Get user roles
     *
     * @param Magento_Core_Model_Abstract $user
     * @return array
     */
    public function getRoles(Magento_Core_Model_Abstract $user)
    {
        if ( !$user->getId() ) {
            return array();
        }

        $table  = $this->getTable('admin_role');
        $adapter   = $this->_getReadAdapter();

        $select = $adapter->select()
                    ->from($table, array())
                    ->joinLeft(
                        array('ar' => $table),
                        "(ar.role_id = {$table}.parent_id and ar.role_type = 'G')",
                        array('role_id'))
                    ->where("{$table}.user_id = :user_id");

        $binds = array(
            'user_id' => (int) $user->getId(),
        );

        $roles = $adapter->fetchCol($select, $binds);

        if ($roles) {
            return $roles;
        }

        return array();
    }


    /**
     * Delete user role
     *
     * @param Magento_Core_Model_Abstract $user
     * @return Magento_User_Model_Resource_User
     */
    public function deleteFromRole(Magento_Core_Model_Abstract $user)
    {
        if ( $user->getUserId() <= 0 ) {
            return $this;
        }
        if ( $user->getRoleId() <= 0 ) {
            return $this;
        }

        $dbh = $this->_getWriteAdapter();

        $condition = array(
            'user_id = ?'   => (int) $user->getId(),
            'parent_id = ?' => (int) $user->getRoleId(),
        );

        $dbh->delete($this->getTable('admin_role'), $condition);
        return $this;
    }

    /**
     * Check if role user exists
     *
     * @param Magento_Core_Model_Abstract $user
     * @return array|false
     */
    public function roleUserExists(Magento_Core_Model_Abstract $user)
    {
        if ( $user->getUserId() > 0 ) {
            $roleTable = $this->getTable('admin_role');

            $dbh = $this->_getReadAdapter();

            $binds = array(
                'parent_id' => $user->getRoleId(),
                'user_id'   => $user->getUserId(),
            );

            $select = $dbh->select()->from($roleTable)
                ->where('parent_id = :parent_id')
                ->where('user_id = :user_id');

            return $dbh->fetchCol($select, $binds);
        } else {
            return array();
        }
    }

    /**
     * Check if user exists
     *
     * @param Magento_Core_Model_Abstract $user
     * @return array|false
     */
    public function userExists(Magento_Core_Model_Abstract $user)
    {
        $adapter = $this->_getReadAdapter();
        $select = $adapter->select();

        $binds = array(
            'username' => $user->getUsername(),
            'email'    => $user->getEmail(),
            'user_id'  => (int) $user->getId(),
        );

        $select->from($this->getMainTable())
            ->where('(username = :username OR email = :email)')
            ->where('user_id <> :user_id');

        return $adapter->fetchRow($select, $binds);
    }

    /**
     * Whether a user's identity is confirmed
     *
     * @param Magento_Core_Model_Abstract $user
     * @return bool
     */
    public function isUserUnique(Magento_Core_Model_Abstract $user)
    {
        return !$this->userExists($user);
    }

    /**
     * Save user extra data
     *
     * @param Magento_Core_Model_Abstract $object
     * @param string $data
     * @return Magento_User_Model_Resource_User
     */
    public function saveExtra($object, $data)
    {
        if ($object->getId()) {
            $this->_getWriteAdapter()->update(
                $this->getMainTable(),
                array('extra' => $data),
                array('user_id = ?' => (int) $object->getId())
            );
        }

        return $this;
    }

    /**
     * Retrieve the total user count bypassing any filters applied to collections
     *
     * @return int
     */
    public function countAll()
    {
        $adapter = $this->_getReadAdapter();
        $select = $adapter->select();
        $select->from($this->getMainTable(), 'COUNT(*)');
        $result = (int)$adapter->fetchOne($select);
        return $result;
    }

    /**
     * Add validation rules to be applied before saving an entity
     *
     * @return Zend_Validate_Interface $validator
     */
    public function getValidationRulesBeforeSave()
    {
        $userIdentity = new Zend_Validate_Callback(array($this, 'isUserUnique'));
        $userIdentity->setMessage(
            __('A user with the same user name or email already exists.'),
            Zend_Validate_Callback::INVALID_VALUE
        );

        return $userIdentity;
    }
}
