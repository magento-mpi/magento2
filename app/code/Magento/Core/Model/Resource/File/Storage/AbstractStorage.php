<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Abstract storage resource model
 *
 * @category    Magento
 * @package     Magento_Core
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Core\Model\Resource\File\Storage;

abstract class AbstractStorage extends \Magento\Core\Model\Resource\Db\AbstractDb
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
     * @return \Magento\Core\Model\Resource\File\Storage\AbstractStorage
     */
    public function setConnectionName($name)
    {
        $this->_connectionName = $name;
        return $this;
    }

    /**
     * Retrieve connection for read data
     *
     * @return \Magento\DB\Adapter\AdapterInterface
     */
    protected function _getReadAdapter()
    {
        return $this->_getConnection($this->_connectionName);
    }

    /**
     * Retrieve connection for write data
     *
     * @return \Magento\DB\Adapter\AdapterInterface
     */
    protected function _getWriteAdapter()
    {
        return $this->_getConnection($this->_connectionName);
    }

    /**
     * Get connection by name or type
     *
     * @param string $connectionName
     * @return \Magento\DB\Adapter\AdapterInterface
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
