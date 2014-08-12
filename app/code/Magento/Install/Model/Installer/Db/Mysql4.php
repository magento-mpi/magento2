<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Mysql resource data model
 */
namespace Magento\Install\Model\Installer\Db;

class Mysql4 extends \Magento\Install\Model\Installer\Db\AbstractDb
{
    /**
     * Retrieve DB server version
     *
     * @return string (string version number | 'undefined')
     */
    public function getVersion()
    {
        $version = $this->_getConnection()->fetchOne('SELECT VERSION()');
        $version = $version ? $version : 'undefined';
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
        $variables = $this->_getConnection()->fetchPairs('SHOW ENGINES');
        return isset($variables['InnoDB']) && ($variables['InnoDB'] == 'DEFAULT' || $variables['InnoDB'] == 'YES');
    }

    /**
     * {@inheritdoc}
     */
    public function cleanUpDatabase()
    {
        $connectionData = $this->getConnectionData();
        $connection = $this->_getConnection();
        $connection->query('DROP DATABASE IF EXISTS ' . $connectionData['dbName']);
        $connection->query('CREATE DATABASE ' . $connectionData['dbName']);
    }
}
