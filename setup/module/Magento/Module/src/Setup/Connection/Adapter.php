<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Module\Setup\Connection;

use Magento\Setup\Framework\DB\Adapter\Pdo\Mysql;
use Magento\Module\Setup\Config;

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
                'driver' => 'Pdo',
                'dsn' => "mysql:dbname=" . $config[Config::KEY_DB_NAME] . ";host=" . $config[Config::KEY_DB_HOST],
                'username' => $config[Config::KEY_DB_USER],
                'password' => isset($config[Config::KEY_DB_PASS]) ? $config[Config::KEY_DB_PASS] : null,
                'driver_options' => [
                    \PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'UTF8'"]
            ]
        );
    }
}
