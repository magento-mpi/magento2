<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Install
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Mysql resource data model
 *
 * @category   Mage
 * @package    Mage_Install
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Install_Model_Installer_Db_Mysql4 extends Mage_Install_Model_Installer_Db_Abstract
{
    /**
     * Retrieve DB server version
     *
     * @return string (string version number | 'undefined')
     */
    public function getVersion()
    {
        $version  = $this->_getConnection()
            ->fetchOne('SELECT VERSION()');
        $version    = $version ? $version : 'undefined';
        $match = array();
        if (preg_match("#^([0-9\.]+)#", $version, $match)) {
            $version = $match[0];
        }
        return $version;
    }

    /**
     * Check InnoDB support
     *
     * @return bool
     */
    public function supportEngine()
    {
        $variables  = $this->_getConnection()
            ->fetchPairs('SHOW VARIABLES');
        return (!isset($variables['have_innodb']) || $variables['have_innodb'] != 'YES') ? false : true;
    }

    /**
     * Clean database
     *
     * @return Mage_Install_Model_Installer_Db_Mysql4
     */
    public  function  cleanDatabase()
    {
        $connection = $this->_getConnection();
        $config = $connection->getConfig();
        $dbName = $connection->quoteIdentifier($config['dbname']);

        $connection->query('DROP DATABASE IF EXISTS ' . $dbName);
        $connection->query('CREATE DATABASE ' . $dbName);

        return $this;
    }
}
