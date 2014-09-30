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
        $adminData = [
            'firstname' => $this->data[self::KEY_FIRST_NAME],
            'lastname' => $this->data[self::KEY_LAST_NAME],
            'username' => $this->data[self::KEY_USERNAME],
            'password' => $this->generatePassword(),
            'email' => $this->data[self::KEY_EMAIL],
            'created' => date('Y-m-d H:i:s'),
            'modified' => date('Y-m-d H:i:s'),
            'extra' => serialize(null),
            'is_active' => 1,
        ];
        $this->setup->getConnection()->insert(
            $this->setup->getTable('admin_user'),
            $adminData,
            true
        );
        $adminId = $this->setup->getConnection()->getDriver()->getLastGeneratedValue();

        // Create the user role, do not duplicate if it already exists
        $adminRoleData = [
            'parent_id' => 1,
            'tree_level' => 2,
            'sort_order' => 0,
            'role_type' => User::ROLE_TYPE,
            'user_id' => $adminId,
            'user_type' => UserContextInterface::USER_TYPE_ADMIN,
            'role_name' => $this->data[self::KEY_USERNAME],
        ];

        $resultSet = $this->setup->getConnection()->query(
            'SELECT * FROM ' . $this->setup->getTable('authorization_role') . ' ' .
            'WHERE parent_id = :parent_id AND tree_level = :tree_level AND sort_order = :sort_order AND ' .
            'role_type = :role_type AND user_id = :user_id AND user_type = :user_type AND role_name = :role_name',
            $adminRoleData
        );
        if ($resultSet->count() < 1) {
            $this->setup->getConnection()->insert($this->setup->getTable('authorization_role'), $adminRoleData, true);
        }
    }
}
