<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Module\Setup\Connection;

use Magento\Db\Adapter\Pdo\Mysql;

class Adapter implements AdapterInterface
{
    /**
     * Get connection
     *
     * @param array $config
     * @return \Magento\Framework\DB\Adapter\AdapterInterface|null
     */
    public function getConnection(array $config = array())
    {
        return new Mysql(
            [
                'driver'         => "Pdo",
                'dsn'            => "mysql:dbname=" . $config['name']. ";host=" .$config['host'],
                'username'       => $config['user'],
                'password'       => isset($config['password']) ? $config['password'] : null,
                'driver_options' => [
                    \PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'UTF8'"]
            ]
        );
    }
}
