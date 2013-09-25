<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */ 
class Magento_Core_Model_Resource_ConnectionPool 
{
    const DEFAULT_CONNECTION = 'default_setup';

    /**
     * @var Magento_Core_Model_Resource_ConnectionAdapterInterface[]
     */
    protected $_pool;

    /**
     * @var Magento_Core_Model_ConnectionAdapterFactory
     */
    protected $_factory;

    /**
     * @var array
     */
    protected $_connectionList;

    /**
     * @param Magento_Core_Model_ConnectionAdapterFactory $factory
     * @param array $connectionList
     */
    public function __construct(
        Magento_Core_Model_ConnectionAdapterFactory $factory,
        array $connectionList = array())
    {
        $this->_factory = $factory;
        $this->_connectionList = $connectionList;
    }

    /**
     * Get connection instance
     *
     * @param string $name
     * @return Magento_Core_Model_Resource_ConnectionAdapterInterface
     */
    public function get($name)
    {
        if (!isset($this->_pool[$name])) {
            if ($this->_hasParent($name)) {
                return $this->_getParentConnection($name);
            } else {
                $instanceName = $this->_getConnectionInstanceName($name);
                if ($instanceName) {
                    $instance = $this->_createNewConnection($instanceName);
                } else {
                    $instance = $this->get(self::DEFAULT_CONNECTION);
                }
                $this->_pool[$name] = $instance;
            }
        }
        return $this->_pool[$name];
    }

    protected function _getParentConnection($name)
    {
        $parentName = $this->_getParentName($name);
        if (isset($this->_pool[$parentName])) {
            $connection = $this->_pool[$parentName];
        } else {
            $connection = $this->get($parentName);
            $this->_pool[$name] = $connection;
        }

        return $connection;
    }

    /**
     * Get connection instance name
     *
     * @param string $name
     * @return string
     */
    protected function _getConnectionInstanceName($name)
    {
        return isset($this->_connectionList[$name]['instance'])
            ? $this->_connectionList[$name]['instance']
            : null;
    }

    /**
     * Get parent connection name
     *
     * @param string $name
     * @return string
     */
    protected function _getParentName($name)
    {
        return $this->_connectionList[$name]['use'];
    }


    /**
     * Check whether connection has parent
     *
     * @param string $name
     * @return bool
     */
    protected function _hasParent($name)
    {
        return isset($this->_connectionList[$name]['use']);
    }

    /**
     * Create new connection instance
     *
     * @param string $name
     * @return Magento_DB_Adapter_Interface
     */
    protected function _createNewConnection($name)
    {
        return $this->_factory->create($name)->getConnection();
    }
}
