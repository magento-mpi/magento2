<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Install
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Mysql resource data model
 */
class Magento_Install_Model_Installer_Db_Mysql4 extends Magento_Install_Model_Installer_Db_Abstract
{
    public function __construct(Magento_Core_Model_Resource $resource, array $dbExtensions = array())
    {
        parent::__construct($resource, $dbExtensions);
    }

    /**
     * Retrieve DB server version
     *
     * @return string (string version number | 'undefined')
     */
    public function getVersion()
    {
        $version  = $this->_getConnection()->fetchOne('SELECT VERSION()');
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
        $variables  = $this->_getConnection()->fetchPairs('SHOW ENGINES');
        return isset($variables['InnoDB']) && ($variables['InnoDB'] == 'DEFAULT' || $variables['InnoDB'] == 'YES');
    }

    /**
     * Clean database
     *
     * @param SimpleXMLElement $config
     * @return Magento_Install_Model_Installer_Db_Abstract
     */
    public function cleanUpDatabase(SimpleXMLElement $config)
    {
        $connection = $this->_resource->getConnection(Magento_Core_Model_Resource::DEFAULT_SETUP_RESOURCE);
        $dbName = $config->dbname;

        $connection->query('DROP DATABASE IF EXISTS ' . $dbName);
        $connection->query('CREATE DATABASE ' . $dbName);

        return $this;
    }
}
