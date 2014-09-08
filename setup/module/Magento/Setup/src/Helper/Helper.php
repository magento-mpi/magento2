<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

namespace Magento\Setup\Helper;

use Magento\Setup\Model\DatabaseCheck;

class Helper
{
    /**
     * Checks Database Connection
     *
     * @param string $dbName
     * @param string $dbHost
     * @param string $dbUser
     * @param string $dbPass
     * @return boolean
     * @throws \Exception
     */
    public static function checkDatabaseConnection($dbName, $dbHost, $dbUser, $dbPass = '')
    {
        //Check DB connection
        $dbConnectionInfo = array(
            'driver' => "Pdo",
            'dsn' => "mysql:dbname=" . $dbName . ";host=" . $dbHost,
            'username' => $dbUser,
            'password' => $dbPass,
            'driver_options' => array(
                \PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'UTF8'"
            ),
        );
        $checkDB = new DatabaseCheck($dbConnectionInfo);
        if (!$checkDB->checkConnection()) {
            throw new \Exception('Database connection failure.');
        }
        return true;
    }
}
