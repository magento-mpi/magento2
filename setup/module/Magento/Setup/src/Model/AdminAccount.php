<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

namespace Magento\Setup\Model;

use Magento\Framework\Math\Random;
use Magento\Setup\Module\Setup;
use Magento\Authorization\Model\Acl\Role\User;
use Magento\Authorization\Model\Acl\Role\Group;
use Magento\Authorization\Model\UserContextInterface;

class AdminAccount
{
    /**#@+
     * Data keys
     */
    const KEY_USERNAME = 'admin_username';
    const KEY_PASSWORD = 'admin_password';
    const KEY_EMAIL = 'admin_email';
    const KEY_FIRST_NAME = 'admin_firstname';
    const KEY_LAST_NAME = 'admin_lastname';
    /**#@- */

    /**
     * Setup
     *
     * @var Setup
     */
    private $setup;

    /**
     * Configurations
     *
     * @var []
     */
    private $data;

    /**
     * Random Generator
     *
     * @var \Magento\Framework\Math\Random
     */
    private $random;

    /**
     * Default Constructor
     *
     * @param Setup $setup
     * @param Random $random
     * @param array $data
     */
    public function __construct(Setup $setup, Random $random, array $data)
    {
        $this->setup  = $setup;
        $this->random = $random;
        $this->data = $data;
    }

    /**
     * Generate password string
     *
     * @return string
     */
    protected function generatePassword()
    {
        $salt = $this->random->getRandomString(32);
        return md5($salt . $this->data[self::KEY_PASSWORD]) . ':' . $salt;
    }

    /**
     * Save administrator account and user role to DB.
     *
     * If the administrator account exists, update it.
     *
     * @return void
     */
    public function save()
    {
        $adminId = $this->saveAdminUser();
        $this->saveAdminUserRole($adminId);
    }

    /**
     * Uses the information in data[] to create the admin user.
     *
     * If the username already exists, it will update the record with information from data[]
     * and set the is_active flag.
     *
     * @return integer The admin user id
     */
    private function saveAdminUser()
    {
        $adminData = [
            'firstname' => $this->data[self::KEY_FIRST_NAME],
            'lastname'  => $this->data[self::KEY_LAST_NAME],
            'username'  => $this->data[self::KEY_USERNAME],
            'password'  => $this->generatePassword(),
            'email'     => $this->data[self::KEY_EMAIL],
            'is_active' => 1,
        ];
        $resultSet = $this->setup->getConnection()->query(
            'SELECT user_id FROM ' . $this->setup->getTable('admin_user') . ' ' .
            'WHERE username = :username',
            ['username' => $this->data[self::KEY_USERNAME]]
        );

        if ($resultSet->count() > 0) {
            // User exists, update
            $adminId = $resultSet->current()->user_id;
            $adminData['modified'] = date('Y-m-d H:i:s');
            $this->setup->getConnection()->update(
                $this->setup->getTable('admin_user'),
                $adminData,
                $this->setup->getConnection()->quoteInto('username = ?', $this->data[self::KEY_USERNAME])
            );
        } else {
            // User does not exist, create it
            $adminData['extra'] = serialize(null);
            $this->setup->getConnection()->insert(
                $this->setup->getTable('admin_user'),
                $adminData
            );
            $adminId = $this->setup->getConnection()->getDriver()->getLastGeneratedValue();
        }
        return $adminId;
    }

    /**
     * Creates the admin user role if one does not exist.
     *
     * Do nothing if a role already exists for this user
     *
     * @param integer $adminId User id of administrator to set role for
     * @return void
     */
    private function saveAdminUserRole($adminId)
    {
        $resultSet = $this->setup->getConnection()->query(
            'SELECT * FROM ' . $this->setup->getTable('authorization_role') . ' ' .
            'WHERE user_id = :user_id',
            ['user_id' => $adminId]
        );
        if ($resultSet->count() < 1) {
            // No user role exists for this user id, create it
            $adminRoleData = [
                'parent_id'  => $this->retrieveAdministratorsRoleId(),
                'tree_level' => 2,
                'role_type'  => User::ROLE_TYPE,
                'user_id'    => $adminId,
                'user_type'  => UserContextInterface::USER_TYPE_ADMIN,
                'role_name'  => $this->data[self::KEY_USERNAME],
            ];
            $this->setup->getConnection()->insert($this->setup->getTable('authorization_role'), $adminRoleData);
        }
    }

    /**
     * Gets the "Administrators" role id, the special role created by data fixture in Authorization module.
     *
     * @return integer The id of the Administrators role
     * @throws \Exception If Administrators role not found or problem connecting with database.
     */
    private function retrieveAdministratorsRoleId()
    {
        // Get Administrators role id to use as parent_id
        $administratorsRoleData = [
            'parent_id'  => 0,
            'tree_level' => 1,
            'role_type' => Group::ROLE_TYPE,
            'user_id' => 0,
            'user_type' => UserContextInterface::USER_TYPE_ADMIN,
            'role_name' => 'Administrators'
        ];

        $resultSet = $this->setup->getConnection()->query(
            'SELECT * FROM ' . $this->setup->getTable('authorization_role') . ' ' .
            'WHERE parent_id = :parent_id AND tree_level = :tree_level AND role_type = :role_type AND ' .
            'user_id = :user_id AND user_type = :user_type AND role_name = :role_name',
            $administratorsRoleData
        );
        if ($resultSet->count() < 1) {
            throw new \Exception('No Administrators role was found, data fixture needs to be run');
        } else {
            // Found at least one, use first
            return $resultSet->current()->role_id;
        }
    }
}
