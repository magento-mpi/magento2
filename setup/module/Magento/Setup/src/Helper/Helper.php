<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

namespace Magento\Setup\Helper;

use Magento\Setup\Model\DatabaseCheck;

/**
 * Helper Class
 *
 * @package Magento\Setup\Helper
 */
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

    /**
     * Finds the executable path for PHP
     * @return string
     * @throws \Exception
     */
    public static function phpExecutablePath()
    {
        try {
            $phpPath = '';
            $iniFile = fopen(php_ini_loaded_file(), 'r');
            while ($line = fgets($iniFile)) {
                if ((strpos($line, 'extension_dir') !== false) && (strrpos($line, ";") !==0)) {
                    $extPath = explode("=", $line);
                    $pathFull = explode("\"", $extPath[1]);
                    $pathParts = str_replace('\\', '/', $pathFull[1]);
                    foreach (explode('/', $pathParts) as $piece) {
                        if ((file_exists($phpPath . 'php') && !is_dir($phpPath . 'php'))
                            || (file_exists($phpPath . 'php.exe') && !is_dir($phpPath . 'php.exe'))) {
                            break;
                        } else if ((file_exists($phpPath . 'bin/php') && !is_dir($phpPath . 'bin/php'))
                            || (file_exists($phpPath . 'bin/php.exe') && !is_dir($phpPath . 'bin/php.exe'))) {
                            $phpPath .= 'bin' . '/';
                            break;
                        } else {
                            $phpPath .= $piece . '/';
                        }
                    }
                    break;
                }
            }
            fclose($iniFile);
        } catch(\Exception $e){
            throw $e;
        }

        return $phpPath;
    }
}
