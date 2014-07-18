<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

namespace Magento\Setup\Model;

use Magento\Framework\Math\Random;
use Magento\Module\Setup\Connection\AdapterInterface;

class AdminAccount
{
    /**
     * @var AdapterInterface
     */
    protected $connection;

    /**
     * @var []
     */
    protected $config;

    /**
     * @var \Magento\Framework\Math\Random
     */
    protected $random;

    /**
     * @param AdapterInterface $connection
     * @param Random $random
     * @param array $config
     */
    public function __construct(
        AdapterInterface $connection,
        Random $random,
        array $config = array()
    ) {
        $this->connection = $connection->getConnection($config['db']);
        $this->config = $config;
        $this->random = $random;
    }

    /**
     * Generate password string
     *
     * @return string
     */
    protected function generatePassword()
    {
        $salt = $this->random->getRandomString(32);
        return md5($salt . $this->config['admin']['password']) . ':' . $salt;
    }

    /**
     * Save administrator account to DB
     *
     * @return void
     */
    public function save()
    {
        $adminData = [
            'firstname' => $this->config['admin']['username'],
            'lastname' => $this->config['admin']['username'],
            'username' => $this->config['admin']['username'],
            'password' => $this->generatePassword(),
            'email' => $this->config['admin']['email'],
            'created' => date('Y-m-d H:i:s'),
            'modified' => date('Y-m-d H:i:s'),
            'extra' => serialize(null),
            'is_active' => 1,
        ];
        $this->connection->insert('admin_user', $adminData);
        $adminId = $this->connection->getDriver()->getLastGeneratedValue();

        $roleData = [
            'parent_id' => 1,
            'tree_level' => 2,
            'sort_order' => 0,
            'role_type' => 'U',
            'user_id' => $adminId,
            'role_name' => $this->config['admin']['username'],
        ];
        $this->connection->insert('admin_role', $roleData);
    }
}
