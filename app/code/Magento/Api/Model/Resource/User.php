<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Api
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * ACL user resource
 *
 * @category    Magento
 * @package     Magento_Api
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Api_Model_Resource_User extends Magento_Core_Model_Resource_Db_Abstract
{
    /**
     * Resource initialization
     *
     */
    protected function _construct()
    {
        $this->_init('api_user', 'user_id');
    }

    /**
     * Initialize unique fields
     *
     * @return Magento_Api_Model_Resource_User
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
     * @param Magento_Api_Model_User $user
     * @return Magento_Api_Model_Resource_User
     */
    public function recordLogin(Magento_Api_Model_User $user)
    {
        $data = array(
            'lognum'  => $user->getLognum()+1,
        );
        $condition = $this->_getReadAdapter()->quoteInto('user_id=?', $user->getUserId());
        $this->_getWriteAdapter()->update($this->getTable('api_user'), $data, $condition);
        return $this;
    }

    /**
     * Record api user session
     *
     * @param Magento_Api_Model_User $user
     * @return Magento_Api_Model_Resource_User
     */
    public function recordSession(Magento_Api_Model_User $user)
    {
        $readAdapter    = $this->_getReadAdapter();
        $writeAdapter   = $this->_getWriteAdapter();
        $select = $readAdapter->select()
            ->from($this->getTable('api_session'), 'user_id')
            ->where('user_id = ?', $user->getId())
            ->where('sessid = ?', $user->getSessid());
        $loginDate = now();
        if ($readAdapter->fetchRow($select)) {
            $writeAdapter->update(
                $this->getTable('api_session'),
                array ('logdate' => $loginDate),
                $readAdapter->quoteInto('user_id = ?', $user->getId()) . ' AND '
                . $readAdapter->quoteInto('sessid = ?', $user->getSessid())
            );
        } else {
            $writeAdapter->insert(
                $this->getTable('api_session'),
                array(
                    'user_id' => $user->getId(),
                    'logdate' => $loginDate,
                    'sessid' => $user->getSessid()
                )
            );
        }
        $user->setLogdate($loginDate);
        return $this;
    }

    /**
     * Clean old session
     *
     * @param Magento_Api_Model_User $user
     * @return Magento_Api_Model_Resource_User
     */
    public function cleanOldSessions(Magento_Api_Model_User $user)
    {
        $readAdapter    = $this->_getReadAdapter();
        $writeAdapter   = $this->_getWriteAdapter();
        $timeout        = Mage::getStoreConfig('api/config/session_timeout');
        $timeSubtract     = $readAdapter->getDateAddSql(
            'logdate',
            $timeout,
            \Magento\DB\Adapter\AdapterInterface::INTERVAL_SECOND);
        $writeAdapter->delete(
            $this->getTable('api_session'),
            array('user_id = ?' => $user->getId(), $readAdapter->quote(now()) . ' > '.$timeSubtract)
        );
        return $this;
    }

    /**
     * Load data by username
     *
     * @param string $username
     * @return array
     */
    public function loadByUsername($username)
    {
        $adapter = $this->_getReadAdapter();
        $select = $adapter->select()->from($this->getTable('api_user'))
            ->where('username=:username');
        return $adapter->fetchRow($select, array('username'=>$username));
    }

    /**
     * load by session id
     *
     * @param string $sessId
     * @return array
     */
    public function loadBySessId($sessId)
    {
        $result = array();
        $adapter = $this->_getReadAdapter();
        $select = $adapter->select()->from($this->getTable('api_session'))->where('sessid = ?', $sessId);
        $apiSession = $adapter->fetchRow($select);
        if ($apiSession) {
            $selectUser = $adapter->select()
                ->from($this->getTable('api_user'))
                ->where('user_id = ?', $apiSession['user_id']);
            $user = $adapter->fetchRow($selectUser);
            if ($user) {
                $result = array_merge($user, $apiSession);
            }
        }
        return $result;
    }

    /**
     * Clear by session
     *
     * @param string $sessid
     * @return Magento_Api_Model_Resource_User
     */
    public function clearBySessId($sessid)
    {
        $this->_getWriteAdapter()->delete(
            $this->getTable('api_session'),
            array('sessid = ?' => $sessid)
        );
        return $this;
    }

    /**
     * Retrieve api user role data if it was assigned to role
     *
     * @param int|Magento_Api_Model_User $user
     * @return null|array
     */
    public function hasAssigned2Role($user)
    {
        $userId = null;
        $result = null;
        if (is_numeric($user)) {
            $userId = $user;
        } else if ($user instanceof Magento_Core_Model_Abstract) {
            $userId = $user->getUserId();
        }

        if ($userId) {
            $adapter = $this->_getReadAdapter();
            $select = $adapter->select();
            $select->from($this->getTable('api_role'))
                ->where('parent_id > 0 AND user_id = ?', $userId);
            $result = $adapter->fetchAll($select);
        }
        return $result;
    }

    /**
     * Action before save
     *
     * @param Magento_Core_Model_Abstract $user
     * @return Magento_Api_Model_Resource_User
     */
    protected function _beforeSave(Magento_Core_Model_Abstract $user)
    {
        if (!$user->getId()) {
            $user->setCreated(now());
        }
        $user->setModified(now());
        return $this;
    }

    /**
     * Delete the object
     *
     * @param Magento_Core_Model_Abstract $user
     * @return boolean
     * @throws Exception|Magento_Core_Exception
     */
    public function delete(Magento_Core_Model_Abstract $user)
    {
        $dbh = $this->_getWriteAdapter();
        $uid = (int) $user->getId();
        $dbh->beginTransaction();
        try {
            $dbh->delete($this->getTable('api_user'), array('user_id = ?' => $uid));
            $dbh->delete($this->getTable('api_role'), array('user_id = ?' => $uid));
        } catch (Magento_Core_Exception $e) {
            throw $e;
            return false;
        } catch (Exception $e) {
            $dbh->rollBack();
            return false;
        }
        $dbh->commit();
        return true;
    }

    /**
     * Save user roles
     *
     * @param Magento_Core_Model_Abstract $user
     * @return $this|\Magento_Core_Model_Abstract
     * @throws Exception|Magento_Core_Exception
     */
    public function _saveRelations(Magento_Core_Model_Abstract $user)
    {
        $rolesIds = $user->getRoleIds();
        if (!is_array($rolesIds) || count($rolesIds) == 0) {
            return $user;
        }

        $adapter = $this->_getWriteAdapter();

        $adapter->beginTransaction();

        try {
            $adapter->delete(
                $this->getTable('api_role'),
                array('user_id = ?' => (int) $user->getId()));
            foreach ($rolesIds as $rid) {
                $rid = intval($rid);
                if ($rid > 0) {
                    //$row = $this->load($user, $rid);
                } else {
                    $row = array('tree_level' => 0);
                }
                $row = array('tree_level' => 0);

                $data = array(
                    'parent_id'     => $rid,
                    'tree_level'    => $row['tree_level'] + 1,
                    'sort_order'    => 0,
                    'role_type'     => Magento_Api_Model_Acl::ROLE_TYPE_USER,
                    'user_id'       => $user->getId(),
                    'role_name'     => $user->getFirstname()
                );
                $adapter->insert($this->getTable('api_role'), $data);
            }
            $adapter->commit();
        } catch (Magento_Core_Exception $e) {
            throw $e;
        } catch (Exception $e) {
            $adapter->rollBack();
        }
        return $this;
    }

    /**
     * Retrieve roles data
     *
     * @param Magento_Core_Model_Abstract $user
     * @return array
     */
    public function _getRoles(Magento_Core_Model_Abstract $user)
    {
        if (!$user->getId()) {
            return array();
        }
        $table   = $this->getTable('api_role');
        $adapter = $this->_getReadAdapter();
        $select  = $adapter->select()
            ->from($table, array())
            ->joinLeft(
                array('ar' => $table),
                $adapter->quoteInto(
                    "ar.role_id = {$table}.parent_id AND ar.role_type = ?",
                    Magento_Api_Model_Acl::ROLE_TYPE_GROUP),
                array('role_id'))
            ->where("{$table}.user_id = ?", $user->getId());

        return (($roles = $adapter->fetchCol($select)) ? $roles : array());
    }

    /**
     * Add Role
     *
     * @param Magento_Core_Model_Abstract $user
     * @return Magento_Api_Model_Resource_User
     */
    public function add(Magento_Core_Model_Abstract $user)
    {
        $adapter = $this->_getWriteAdapter();
        $aRoles  = $this->hasAssigned2Role($user);
        if (sizeof($aRoles) > 0) {
            foreach ($aRoles as $data) {
                $adapter->delete(
                    $this->getTable('api_role'),
                    array('role_id = ?' => $data['role_id'])
                );
            }
        }

        if ($user->getId() > 0) {
            $role = Mage::getModel('Magento_Api_Model_Role')->load($user->getRoleId());
        } else {
            $role = new \Magento\Object(array('tree_level' => 0));
        }
        $adapter->insert($this->getTable('api_role'), array(
            'parent_id' => $user->getRoleId(),
            'tree_level'=> ($role->getTreeLevel() + 1),
            'sort_order'=> 0,
            'role_type' => Magento_Api_Model_Acl::ROLE_TYPE_USER,
            'user_id'   => $user->getUserId(),
            'role_name' => $user->getFirstname()
        ));

        return $this;
    }

    /**
     * Delete from role
     *
     * @param Magento_Core_Model_Abstract $user
     * @return Magento_Api_Model_Resource_User
     */
    public function deleteFromRole(Magento_Core_Model_Abstract $user)
    {
        if ($user->getUserId() <= 0) {
            return $this;
        }
        if ($user->getRoleId() <= 0) {
            return $this;
        };

        $adapter   = $this->_getWriteAdapter();
        $table     = $this->getTable('api_role');

        $condition = array(
            "{$table}.user_id = ?"  => $user->getUserId(),
            "{$table}.parent_id = ?"=> $user->getRoleId()
        );
        $adapter->delete($table, $condition);
        return $this;
    }

    /**
     * Retrieve roles which exists for user
     *
     * @param Magento_Core_Model_Abstract $user
     * @return array
     */
    public function roleUserExists(Magento_Core_Model_Abstract $user)
    {
        $result = array();
        if ($user->getUserId() > 0) {
            $adapter    = $this->_getReadAdapter();
            $select     = $adapter->select()->from($this->getTable('api_role'))
                ->where('parent_id = ?', $user->getRoleId())
                ->where('user_id = ?', $user->getUserId());
            $result = $adapter->fetchCol($select);
        }
        return $result;
    }

    /**
     * Check if user not unique
     *
     * @param Magento_Core_Model_Abstract $user
     * @return array
     */
    public function userExists(Magento_Core_Model_Abstract $user)
    {
        $usersTable = $this->getTable('api_user');
        $adapter    = $this->_getReadAdapter();
        $condition  = array(
            $adapter->quoteInto("{$usersTable}.username = ?", $user->getUsername()),
            $adapter->quoteInto("{$usersTable}.email = ?", $user->getEmail()),
        );
        $select = $adapter->select()
            ->from($usersTable)
            ->where(implode(' OR ', $condition))
            ->where($usersTable . '.user_id != ?', (int)$user->getId());
        return $adapter->fetchRow($select);
    }
}
