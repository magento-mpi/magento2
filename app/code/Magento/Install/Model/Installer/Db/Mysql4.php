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
    /**
     * @param Magento_Core_Model_Resource $resource
     * @param Magento_Core_Model_ResourceFactory $resourceFactory
     * @param array $dbExtensions
     */
    public function __construct(
        Magento_Core_Model_Resource $resource,
        Magento_Core_Model_ResourceFactory $resourceFactory,
        array $dbExtensions = array()
    ) {
        parent::__construct($resource, $dbExtensions);
        $this->_resourceFactory = $resourceFactory;
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
        /** @var $resourceModel Magento_Core_Model_Resource */
        $resourceModel = $this->_resourceFactory->create();
        $connection = $resourceModel->getConnection(Magento_Core_Model_Resource::DEFAULT_SETUP_RESOURCE);
        $dbName = $config->dbname;

        $connection->query('DROP DATABASE IF EXISTS ' . $dbName);
        $connection->query('CREATE DATABASE ' . $dbName);

        return $this;
    }
}
