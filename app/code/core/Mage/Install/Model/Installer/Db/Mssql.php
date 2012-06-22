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
 * Mssql resource data model
 *
 * @category   Mage
 * @package    Mage_Install
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Install_Model_Installer_Db_Mssql extends Mage_Install_Model_Installer_Db_Abstract
{
    /**
     * Retrieve DB server version
     *
     * @return string (string version number | 'undefined')
     */
    public function getVersion()
    {
        /*
         xp_msver, for any option, returns the four-column headings with values for that option
         Index | Name | Internal_Value | Character_Value
        */
        $stmt       = $this->_getConnection()->query('EXEC master..xp_msver ProductVersion');
        $version    = $stmt->fetchColumn(3);
        $version    = $version ? $version : 'undefined';
        return $version;
    }

    /**
     * Retrieve required PHP extension list for database
     *
     * @return array
     */
    public function getRequiredExtensions()
    {
        $extensions = parent::getRequiredExtensions();
        $extensions[] = (string)Mage::getConfig()
                ->getNode(sprintf('install/databases/mssql/pdo_types/%s', $this->getPdoType()));
        return $extensions;
    }

    /**
     * Return pdo type for current OS
     *
     * @return string
     */
    public function getPdoType()
    {
        return (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') ? 'sqlsrv' : 'dblib';
    }

    /**
     * Clean database
     *
     * @param SimpleXMLElement $config
     * @return Mage_Install_Model_Installer_Db_Abstract
     */
    public function cleanUpDatabase(SimpleXMLElement $config)
    {
        $resourceModel = new Mage_Core_Model_Resource();
        $connection = $resourceModel->getConnection(Mage_Core_Model_Resource::DEFAULT_SETUP_RESOURCE);
        $dbName = $config->dbname;
        $query = str_replace(
            '%s',
            $dbName,
            "IF EXISTS (SELECT name FROM sys.databases WHERE name = N'%s')\nDROP DATABASE [%s]\n" .
                "CREATE DATABASE %s\n"
        );
        $connection->query($query);

        return $this;
    }
}

