<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Abstract storage resource model
 *
 * @category    Mage
 * @package     Mage_Core
 * @author      Magento Core Team <core@magentocommerce.com>
 */
abstract class Mage_Core_Model_Resource_File_Storage_Abstract extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * File storage connection name
     *
     * @var string
     */
    protected $_connectionName = null;

    /**
     * Sets name of connection the resource will use
     *
     * @param string $name
     * @return Mage_Core_Model_Resource_File_Storage_Abstract
     */
    public function setConnectionName($name)
    {
        $this->_connectionName = $name;
        return $this;
    }

    /**
     * Retrieve connection for read data
     *
     * @return Magento_DB_Adapter_Interface
     */
    protected function _getReadAdapter()
    {
        return $this->_getConnection($this->_connectionName);
    }

    /**
     * Retrieve connection for write data
     *
     * @return Magento_DB_Adapter_Interface
     */
    protected function _getWriteAdapter()
    {
        return $this->_getConnection($this->_connectionName);
    }

    /**
     * Get connection by name or type
     *
     * @param string $connectionName
     * @return Magento_DB_Adapter_Interface
     */
    protected function _getConnection($connectionName)
    {
        if (isset($this->_connections[$connectionName])) {
            return $this->_connections[$connectionName];
        }

        $this->_connections[$connectionName] = $this->_resources->getConnection($connectionName);

        return $this->_connections[$connectionName];
    }
}
