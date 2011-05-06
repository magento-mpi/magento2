<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Tools
 * @copyright   Copyright (c) 2010 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

require_once dirname(__FILE__) . DIRECTORY_SEPARATOR .  'abstract.php';

/**
 * Magento SQL Server Build Shell Script
 *
 * @category    Mage
 * @package     Mage_Shell
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Tools_Build_Prepare_Mssql extends Mage_Tools_Build_Abstract
{
    /**
     * SQL Server connection
     *
     * @var PDO
     */
    protected $_connect;

    /**
     * Database name
     *
     * @var string
     */
    protected $_database;

    /**
     * Initialize connection to database
     *
     * @return Mage_Shell_Abstract
     */
    protected function _construct()
    {
        $this->_database = $this->getArg('db_name');
        if (empty($this->_database)) {
            echo 'Please define database name';
            exit(1);
        }

        // connection settings
        $host       = $this->getArg('db_host', 'mssql.kiev-dev');
        $port       = $this->getArg('db_port', '1433');
        $username   = $this->getArg('db_user', 'sa');
        $password   = $this->getArg('db_pass', '123123q');
        $dns        = sprintf('dblib:host=%s:%d;dbname=master', $host, $port);

        $this->_connect = new PDO($dns, $username, $password);
        if (!$this->_connect) {
            echo 'Please check connection settings';
            exit(1);
        }

        return parent::_construct();
    }

    /**
     * Drop database if exists and create clean database
     *
     * @return void
     */
    public function prepare()
    {
        // kill session
        $query = <<<SQL
DECLARE @SQL VARCHAR(255);
SELECT COALESCE(@SQL, '') + 'Kill ' + CAST(spid AS VARCHAR(10)) + '; '
FROM sys.sysprocesses
WHERE DBID = DB_ID('{$this->_database}');
EXEC(@SQL);
SQL;

        $stmt = $this->_connect->query($query);
        $stmt->execute();

        // drop database if exists
        $query = <<<SQL
IF EXISTS (SELECT name FROM sys.databases WHERE name = N'{$this->_database}')
    DROP DATABASE [{$this->_database}]
SQL;
        $stmt = $this->_connect->query($query);
        $stmt->execute();

        // create clean database
        $query = "CREATE DATABASE [{$this->_database}]";
        $stmt = $this->_connect->query($query);
        $stmt->execute();

        // grant qa_setup privileges
        $query = "USE [{$this->_database}]";
        $stmt = $this->_connect->query($query);
        $stmt->execute();

        $query = "CREATE USER [qa_setup] FOR LOGIN [qa_setup]";
        $stmt = $this->_connect->query($query);
        $stmt->execute();

        $query = "EXEC sp_addrolemember N'db_owner', N'qa_setup'";
        $stmt = $this->_connect->query($query);
        $stmt->execute();

        // grant qa_read privileges
        $query = "USE [{$this->_database}]";
        $stmt = $this->_connect->query($query);
        $stmt->execute();

        $query = "CREATE USER [qa_read] FOR LOGIN [qa_read]";
        $stmt = $this->_connect->query($query);
        $stmt->execute();

        $query = "EXEC sp_addrolemember N'db_datareader', N'qa_read'";
        $stmt = $this->_connect->query($query);
        $stmt->execute();

        echo 'Database was created succesfully';
    }

    /**
     * Change base URLS
     *
     * @return void
     */
    public function baseUrl()
    {
        $coreCacheTable = 'prfx_core_config_data';
        $buildName      = $this->getArg('build_name');
        $buildNumber    = $this->getArg('build_number');
        $buildDomain    = $_ENV['TEAMCITY_BUILDAGENT_DOMAIN'];
        $secure         = sprintf('http://%s/builds/%s/%s/', $buildDomain, $buildName, $buildNumber);
        $unSecure       = sprintf('http://%s/builds/%s/%s/', $buildDomain, $buildName, $buildNumber);

        // select database
        $query = "USE [{$this->_database}]";
        $stmt = $this->_connect->query($query);
        $stmt->execute();

        $query = "UPDATE [{$coreCacheTable}] SET [value] = '{$unSecure}' WHERE [path] LIKE 'web/unsecure/base_url'";
        $stmt = $this->_connect->query($query);
        $stmt->execute();

        $query = "UPDATE [{$coreCacheTable}] SET [value] = '{$secure}' WHERE [path] LIKE 'web/secure/base_url'";
        $stmt = $this->_connect->query($query);
        $stmt->execute();
    }

    /**
     * Copy database from build
     *
     * @return void
     */
    public function copyOtherBuildDb()
    {

    }
}

$shell = new Mage_Tools_Build_Prepare_Mssql();
if ($shell->getArg('shell') == 'baseurl') {
    $shell->baseUrl();
} else {
    $shell->prepare();
}
