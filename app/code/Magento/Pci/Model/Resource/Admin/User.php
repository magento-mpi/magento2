<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Pci\Model\Resource\Admin;

use Magento\User\Model\User as ModelUser;

/**
 * Admin user resource model
 */
class User extends \Magento\User\Model\Resource\User
{
    /**
     * Unlock specified user record(s)
     *
     * @param int|int[] $userIds
     * @return int number of affected rows
     */
    public function unlock($userIds)
    {
        if (!is_array($userIds)) {
            $userIds = array($userIds);
        }
        return $this->_getWriteAdapter()->update(
            $this->getMainTable(),
            array('failures_num' => 0, 'first_failure' => null, 'lock_expires' => null),
            $this->getIdFieldName() . ' IN (' . $this->_getWriteAdapter()->quote($userIds) . ')'
        );
    }

    /**
     * Lock specified user record(s)
     *
     * @param int|int[] $userIds
     * @param int $exceptId
     * @param int $lifetime
     * @return int number of affected rows
     */
    public function lock($userIds, $exceptId, $lifetime)
    {
        if (!is_array($userIds)) {
            $userIds = array($userIds);
        }
        $exceptId = (int)$exceptId;
        return $this->_getWriteAdapter()->update(
            $this->getMainTable(),
            array('lock_expires' => $this->dateTime->formatDate(time() + $lifetime)),
            "{$this->getIdFieldName()} IN (" . $this->_getWriteAdapter()->quote(
                $userIds
            ) . ")\n            AND {$this->getIdFieldName()} <> {$exceptId}"
        );
    }

    /**
     * Increment failures count along with updating lock expire and first failure dates
     *
     * @param ModelUser $user
     * @param int|false $setLockExpires
     * @param int|false $setFirstFailure
     * @return void
     */
    public function updateFaiure($user, $setLockExpires = false, $setFirstFailure = false)
    {
        $update = array('failures_num' => new \Zend_Db_Expr('failures_num + 1'));
        if (false !== $setFirstFailure) {
            $update['first_failure'] = $this->dateTime->formatDate($setFirstFailure);
            $update['failures_num'] = 1;
        }
        if (false !== $setLockExpires) {
            $update['lock_expires'] = $this->dateTime->formatDate($setLockExpires);
        }
        $this->_getWriteAdapter()->update(
            $this->getMainTable(),
            $update,
            $this->_getWriteAdapter()->quoteInto("{$this->getIdFieldName()} = ?", $user->getId())
        );
    }

    /**
     * Purge and get remaining old password hashes
     *
     * @param ModelUser $user
     * @param int $retainLimit
     * @return array
     */
    public function getOldPasswords($user, $retainLimit = 4)
    {
        $userId = (int)$user->getId();
        $table = $this->getTable('enterprise_admin_passwords');

        // purge expired passwords, except that should retain
        $retainPasswordIds = $this->_getWriteAdapter()->fetchCol(
            $this->_getWriteAdapter()->select()->from(
                $table,
                'password_id'
            )->where(
                'user_id = :user_id'
            )->order(
                'expires ' . \Magento\Framework\DB\Select::SQL_DESC
            )->order(
                'password_id ' . \Magento\Framework\DB\Select::SQL_DESC
            )->limit(
                $retainLimit
            ),
            array(':user_id' => $userId)
        );
        $where = array('user_id = ?' => $userId, 'expires <= ?' => time());
        if ($retainPasswordIds) {
            $where['password_id NOT IN (?)'] = $retainPasswordIds;
        }
        $this->_getWriteAdapter()->delete($table, $where);

        // now get all remained passwords
        return $this->_getReadAdapter()->fetchCol(
            $this->_getReadAdapter()->select()->from($table, 'password_hash')->where('user_id = :user_id'),
            array(':user_id' => $userId)
        );
    }

    /**
     * Remember a password hash for further usage
     *
     * @param ModelUser $user
     * @param string $passwordHash
     * @param int $lifetime
     * @return void
     */
    public function trackPassword($user, $passwordHash, $lifetime)
    {
        $now = time();
        $this->_getWriteAdapter()->insert(
            $this->getTable('enterprise_admin_passwords'),
            array(
                'user_id' => $user->getId(),
                'password_hash' => $passwordHash,
                'expires' => $now + $lifetime,
                'last_updated' => $now
            )
        );
    }

    /**
     * Get latest password for specified user id
     * Possible false positive when password was changed several times with different lifetime configuration
     *
     * @param int $userId
     * @return array
     */
    public function getLatestPassword($userId)
    {
        return $this->_getReadAdapter()->fetchRow(
            $this->_getReadAdapter()->select()->from(
                $this->getTable('enterprise_admin_passwords')
            )->where(
                'user_id = :user_id'
            )->order(
                'password_id ' . \Magento\Framework\DB\Select::SQL_DESC
            )->limit(
                1
            ),
            array(':user_id' => $userId)
        );
    }
}
