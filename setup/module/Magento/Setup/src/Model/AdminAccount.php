<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

namespace Magento\Setup\Model;

use Magento\Framework\Math\Random;
use Magento\Module\Setup;

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
     * Save administrator account to DB
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

        $roles = [
            0 => [
                'parent_id' => 0,
                'tree_level' => 1,
                'sort_order' => 1,
                'role_type' => 'G',
                'user_id' => 0,
                'user_type' => 2,
                'role_name' => 'Administrators',
            ],
            1 => [
                'parent_id' => 1,
                'tree_level' => 2,
                'sort_order' => 0,
                'role_type' => 'U',
                'user_id' => $adminId,
                'user_type' => 2,
                'role_name' => $this->data[self::KEY_USERNAME],
            ]
        ];

        foreach ($roles as $role) {
            $this->setup->getConnection()->insert($this->setup->getTable('authorization_role'), $role, true);
        }
    }
}
