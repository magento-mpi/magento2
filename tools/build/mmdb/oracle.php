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
 * Magento Oracle Build Shell Script
 *
 * @category    Mage
 * @package     Mage_Shell
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Tools_Build_Prepare_Oracle extends Mage_Tools_Build_Abstract
{
    /**
     * SQL Server connection
     *
     * @var resource
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
        $this->_database = strtoupper($this->getArg('db_name'));
        if (empty($this->_database)) {
            echo 'Please define database name';
            exit(1);
        }

        // connection settings
        $hostname   = $this->getArg('db_host', '(DESCRIPTION=(ADDRESS_LIST=(ADDRESS=(PROTOCOL=TCP)(HOST=orcl.kiev-dev)(PORT=1521)))(CONNECT_DATA=(SID=MGNTDB)))');
        $username   = $this->getArg('db_user', 'MAGENTO');
        $password   = $this->getArg('db_pass', '12345');

        $this->_connect = oci_connect($username, $password, $hostname);
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
        $query =  <<<SQL
BEGIN
  FOR cur_session IN (
    SELECT s.SID, s.SERIAL#
    FROM sys.v_\$session s
    WHERE schemaname = UPPER('{$this->_database}')
  )
  LOOP
    dbms_output.put_line('ALTER SYSTEM KILL SESSION ''' || cur_session.sid || ',' || cur_session.serial# || ''' IMMEDIATE');
    EXECUTE IMMEDIATE 'ALTER SYSTEM KILL SESSION ''' || cur_session.sid || ',' || cur_session.serial# || ''' IMMEDIATE';
  END LOOP;
END;

SQL;
        $stmt = oci_parse($this->_connect, $query);
        oci_execute($stmt, OCI_DEFAULT);
        oci_rollback($this->_connect);
        echo "User session was killed successfully\n";

        $query = "SELECT * FROM all_users WHERE USERNAME=UPPER('{$this->_database}')";
        $stmt = oci_parse($this->_connect, $query);
        oci_execute($stmt);
        $user = oci_fetch_row($stmt);
        if ($user) {
            $query = "DROP USER {$this->_database} CASCADE";
            $stmt = oci_parse($this->_connect, $query);
            oci_execute($stmt);
        }

        // create user
        $queries = array(
            "CREATE USER {$this->_database} IDENTIFIED BY \"{$this->_database}\""
                . " DEFAULT TABLESPACE MGNTDB_DATA TEMPORARY TABLESPACE TEMP profile DEFAULT",
            "GRANT CONNECT TO {$this->_database}",
            "GRANT DBA TO {$this->_database}",
            "GRANT RESOURCE TO {$this->_database}",
            "GRANT CREATE PROCEDURE TO {$this->_database}",
            "GRANT CREATE TRIGGER TO {$this->_database}"
        );

        foreach ($queries as $query) {
            $stmt = oci_parse($this->_connect, $query);
            oci_execute($stmt);
        }

        echo "Database was created successfully";
    }

    /**
     * Change base URLS
     *
     * @return void
     */
    public function baseUrl()
    {
        $coreCacheTable = $this->_database . '.prfx_core_config_data';
        $buildName      = $this->getArg('build_name');
        $buildNumber    = $this->getArg('build_number');
        $buildDomain    = $_ENV['TEAMCITY_BUILDAGENT_DOMAIN'];
        $secure         = sprintf('http://%s/builds/%s/%s/', $buildDomain, $buildName, $buildNumber);
        $unSecure       = sprintf('http://%s/builds/%s/%s/', $buildDomain, $buildName, $buildNumber);

        $query = "UPDATE {$coreCacheTable} SET \"VALUE\" = '{$unSecure}' WHERE \"PATH\" LIKE 'web/unsecure/base_url'";
        $stmt = oci_parse($this->_connect, $query);
        oci_execute($stmt);

        $query = "UPDATE {$coreCacheTable} SET \"VALUE\" = '{$secure}' WHERE \"PATH\" LIKE 'web/secure/base_url'";
        $stmt = oci_parse($this->_connect, $query);
        oci_execute($stmt);
    }
}

$shell = new Mage_Tools_Build_Prepare_Oracle();
if ($shell->getArg('shell') == 'baseurl') {
    $shell->baseUrl();
} else {
    $shell->prepare();
}
