<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Module\Setup\Connection;

use Magento\Setup\Framework\DB\Adapter\Pdo\Mysql;

class Adapter implements AdapterInterface
{
    /**
     * Get connection
     *
     * @param array $config
     * @return \Magento\Setup\Framework\DB\Adapter\AdapterInterface|null
     */
    public function getConnection(array $config = array())
    {
        return new Mysql(
            [
                'driver'         => "Pdo",
                'dsn'            => "mysql:dbname=" . $config['db_name'] . ";host=" . $config['db_host'],
                'username'       => $config['db_user'],
                'password'       => isset($config['db_pass']) ? $config['db_pass'] : null,
                'driver_options' => [
                    \PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'UTF8'"]
            ]
        );
    }
}
