<?php
/**
 * Resources and connections registry and factory
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Core_Model_Resource
{
    const AUTO_UPDATE_CACHE_KEY  = 'DB_AUTOUPDATE';
    const AUTO_UPDATE_ONCE       = 0;
    const AUTO_UPDATE_NEVER      = -1;
    const AUTO_UPDATE_ALWAYS     = 1;

    const DEFAULT_READ_RESOURCE  = 'core_read';
    const DEFAULT_WRITE_RESOURCE = 'core_write';
    const DEFAULT_SETUP_RESOURCE = 'core_setup';

    /**
     * Instances of classes for connection types
     *
     * @var array
     */
    protected $_connectionTypes    = array();

    /**
     * Instances of actual connections
     *
     * @var Magento_DB_Adapter_Interface[]
     */
    protected $_connections        = array();

    /**
     * Names of actual connections that wait to set cache
     *
     * @var array
     */
    protected $_skippedConnections = array();

    /**
     * Registry of resource entities
     *
     * @var array
     */
    protected $_entities           = array();

    /**
     * Mapped tables cache array
     *
     * @var array
     */
    protected $_mappedTableNames;

    /**
     * Resource configuration
     *
     * @var Magento_Core_Model_Config_Resource
     */
    protected $_resourceConfig;

    /**
     * Application cache
     *
     * @var Magento_Core_Model_CacheInterface
     */
    protected $_cache;

    /**
     * Dirs instance
     *
     * @var Magento_Core_Model_Dir
     */
    protected $_dirs;

    /**
     * @var Magento_Core_Model_ConnectionFactory
     */
    protected $_connInstanceFactory;

    /**
     * @var array
     */
    protected $_connectionList;

    /**
     * @param Magento_Core_Model_Config_Resource $resourceConfig
     * @param Magento_Core_Model_CacheInterface $cache
     * @param Magento_Core_Model_Dir $dirs
     * @param Magento_Core_Model_ConnectionFactory $connInstanceFactory
     * @param array $connectionList
     */
    public function __construct(
        Magento_Core_Model_Config_Resource $resourceConfig,
        Magento_Core_Model_CacheInterface $cache,
        Magento_Core_Model_Dir $dirs,
        Magento_Core_Model_ConnectionFactory $connInstanceFactory,
        array $connectionList
    ) {
        $this->_resourceConfig = $resourceConfig;
        $this->_connectionList = $connectionList;
        $this->_connInstanceFactory = $connInstanceFactory;
        $this->_cache = $cache;
        $this->_dirs = $dirs;
    }

    /**
     * Set resource configuration
     *
     * @param Magento_Core_Model_Config_Resource $resourceConfig
     */
    public function setResourceConfig(Magento_Core_Model_Config_Resource $resourceConfig)
    {
        $this->_resourceConfig = $resourceConfig;
    }

    /**
     * Set cache instance
     *
     * @param Magento_Core_Model_CacheInterface $cache
     */
    public function setCache(Magento_Core_Model_CacheInterface $cache)
    {
        $this->_cache = $cache;
    }

    /**
     * Creates a connection to resource whenever needed
     *
     * @param string $name
     * @return Magento_DB_Adapter_Interface
     */
    public function getConnection($name)
    {
        if (isset($this->_connections[$name])) {
            $connection = $this->_connections[$name];
            if (isset($this->_skippedConnections[$name])) {
                $connection->setCacheAdapter(Mage::app()->getCache());
                unset($this->_skippedConnections[$name]);
            }
            return $connection;
        }

        if (!isset($this->_connectionList[$name])) {
            $this->_connections[$name] = $this->_getDefaultConnection($name);
            return $this->_connections[$name];
        }
        if (isset($this->_connectionList[$name]['disabled']) && $this->_connectionList[$name]['disabled']) {
            return false;
        }

        $connection = $this->_newConnection($this->_connectionList[$name]);
        if ($connection) {
            $connection->setCacheAdapter($this->_cache->getFrontend());
        }

        $this->_connections[$name] = $connection;

        return $connection;
    }

    /**
     * Create new connection adapter instance by connection type and config
     *
     * @param string $connInstanceName
     * @return Magento_DB_Adapter_Interface|false
     */
    protected function _newConnection($connInstanceName)
    {
        $connection = false;
        // try to get adapter and create connection
        if ($connInstanceName) {
            $connection = $this->_connInstanceFactory->createConnectionInstance($connInstanceName);

            if (!($connection instanceof Magento_DB_Adapter_Interface)) {
                $connection = false;
            }
        }

        return $connection;
    }

    /**
     * Retrieve default connection name by required connection name
     *
     * @param string $requiredConnectionName
     * @return string
     */
    protected function _getDefaultConnection($requiredConnectionName)
    {
        if (strpos($requiredConnectionName, 'read') !== false) {
            return $this->getConnection(self::DEFAULT_READ_RESOURCE);
        }
        return $this->getConnection(self::DEFAULT_WRITE_RESOURCE);
    }

    /**
     * Get resource table name, validated by db adapter
     *
     * @param   string|array $modelEntity
     * @return  string
     */
    public function getTableName($modelEntity)
    {
        $tableSuffix = null;
        if (is_array($modelEntity)) {
            list($modelEntity, $tableSuffix) = $modelEntity;
        }

        $tableName = $modelEntity;

        $mappedTableName = $this->getMappedTableName($tableName);
        if ($mappedTableName) {
            $tableName = $mappedTableName;
        } else {
            $tablePrefix = (string)$this->_resourceConfig->getTablePrefix();
            if ($tablePrefix && strpos($tableName, $tablePrefix) !== 0) {
                $tableName = $tablePrefix . $tableName;
            }
        }

        if ($tableSuffix) {
            $tableName .= '_' . $tableSuffix;
        }
        return $this->getConnection(self::DEFAULT_READ_RESOURCE)->getTableName($tableName);
    }

    /**
     * Set mapped table name
     *
     * @param string $tableName
     * @param string $mappedName
     * @return Magento_Core_Model_Resource
     */
    public function setMappedTableName($tableName, $mappedName)
    {
        $this->_mappedTableNames[$tableName] = $mappedName;
        return $this;
    }

    /**
     * Get mapped table name
     *
     * @param string $tableName
     * @return bool|string
     */
    public function getMappedTableName($tableName)
    {
        if (isset($this->_mappedTableNames[$tableName])) {
            return $this->_mappedTableNames[$tableName];
        } else {
            return false;
        }
    }

    /**
     * Create new connection with custom config
     *
     * @param string $name
     * @param string $connInstanceName
     * @return Magento_DB_Adapter_Interface
     */
    public function createConnection($name, $connInstanceName)
    {
        if (!isset($this->_connections[$name])) {
            $connection = $this->_newConnection($connInstanceName);

            $this->_connections[$name] = $connection;
        }
        return $this->_connections[$name];
    }

    public function checkDbConnection()
    {
    }

    public function getAutoUpdate()
    {
        return self::AUTO_UPDATE_ALWAYS;
    }

    public function setAutoUpdate($value)
    {
        return $this;
    }

    /**
     * Retrieve 32bit UNIQUE HASH for a Table index
     *
     * @param string $tableName
     * @param array|string $fields
     * @param string $indexType
     * @return string
     */
    public function getIdxName($tableName, $fields, $indexType = Magento_DB_Adapter_Interface::INDEX_TYPE_INDEX)
    {
        return $this->getConnection(self::DEFAULT_READ_RESOURCE)
            ->getIndexName($this->getTableName($tableName), $fields, $indexType);
    }

    /**
     * Retrieve 32bit UNIQUE HASH for a Table foreign key
     *
     * @param string $priTableName  the target table name
     * @param string $priColumnName the target table column name
     * @param string $refTableName  the reference table name
     * @param string $refColumnName the reference table column name
     * @return string
     */
    public function getFkName($priTableName, $priColumnName, $refTableName, $refColumnName)
    {
        return $this->getConnection(self::DEFAULT_READ_RESOURCE)
            ->getForeignKeyName($this->getTableName($priTableName), $priColumnName,
                $this->getTableName($refTableName), $refColumnName);
    }
}
