<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

namespace Magento\Setup\Helper;

use Zend\Console\Request as ConsoleRequest;

class Helper
{

    /**
     * Convert an array to string
     *
     * @param array $input
     * @return string
     */
    public static function arrayToString($input)
    {
        $result = '';
        foreach ($input as $key => $value) {
            $result .= "$key => $value\n";
        }

        return $result;
    }

    /**
     * Check existence of a directory
     *
     * @param string $destinationDir
     * @return void
     * @throws \Exception
     */
    public static function checkAndCreateDirectory($destinationDir)
    {
        try {
            if (!is_dir($destinationDir)) {
                mkdir($destinationDir, 0777, true);
            }
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    /**
     * Show installation options
     *
     * @return string
     */
    public static function showInstallationOptions()
    {
        return "\n" . 'Required parameters:' ."\n\n" .
        'license_agreement_accepted => Accept licence. See LICENSE*.txt. Flag value.' ."\n" .
        'locale => Locale to use. Run with "show locales" for full list' ."\n" .
        'timezone => Time zone to use. Run with "show timezones" for full list' ."\n" .
        'currency => Default currency. Run with "show currencies" for full list' ."\n" .
        'db_host => IP or name of your DB host' ."\n" .
        'db_name => Database name' ."\n" .
        'db_user => Database user name' ."\n" .
        'store_url => Store URL. For example, "http://myinstance.com"' ."\n" .
        'admin_url => Admin Front Name. For example, "admin"' ."\n" .
        'admin_lastname => Admin user last name' ."\n" .
        'admin_firstname => Admin user first name' ."\n" .
        'admin_email => Admin email' ."\n" .
        'admin_username => Admin login' ."\n" .
        'admin_password => Admin password' ."\n\n" .
        'Optional parameters:' ."\n\n" .
        'magentoDir => The Magento application directory.' ."\n" .
        'use_rewrites => Use web server rewrites. Value "yes"/"no".' ."\n" .
        'db_pass => DB password. Empty by default' ."\n" .
        'db_table_prefix => Use prefix for tables of this installation. Empty by default' ."\n" .
        'secure_store_url => Full secure URL for store. For example "https://myinstance.com"' ."\n" .
        'secure_admin_url => Full secure URL for admin . For example "https://myinstance.com/admin"' ."\n" .
        'encryption_key => Key to encrypt sensitive data. Auto-generated if not provided' ."\n";
    }

    /**
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
                        $phpPath .= $piece . '/';
                        if (strpos($piece, phpversion()) !== false) {
                            if (file_exists($phpPath . 'bin')) {
                                $phpPath .= 'bin' . '/';
                            }
                            break;
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
