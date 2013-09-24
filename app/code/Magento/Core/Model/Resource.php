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
     * Instances of actual connections
     *
     * @var Magento_DB_Adapter_Interface[]
     */
    protected $_connections = array();

    /**
     * Names of actual connections that wait to set cache
     *
     * @var array
     */
    protected $_skippedConnections = array();

    /**
     * Mapped tables cache array
     *
     * @var array
     */
    protected $_mappedTableNames;

    /**
     * Application cache
     *
     * @var Magento_Core_Model_CacheInterface
     */
    protected $_cache;

    /**
     * @var Magento_Core_Model_ConnectionAdapterFactory
     */
    protected $_connAdapterFactory;

    /**
     * @var array
     */
    protected $_connAdapterPool;

    /**
     * @var string
     */
    protected $_tablePrefix;

    /**
     * @param Magento_Core_Model_CacheInterface $cache
     * @param Magento_Core_Model_ConnectionAdapterFactory $connAdapterFactory
     * @param string $tablePrefix
     */
    public function __construct(
        Magento_Core_Model_CacheInterface $cache,
        Magento_Core_Model_ConnectionAdapterFactory $connAdapterFactory,
        $tablePrefix = ''
    ) {
        $this->_connAdapterFactory = $connAdapterFactory;
        $this->_cache = $cache;
        $this->_tablePrefix = $tablePrefix;
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

        $connection = $this->_newConnection($name);
        if (!$connection) {
            $connection = $this->_getDefaultConnection($name);
        }

        if ($connection) {
            $connection->setCacheAdapter($this->_cache->getFrontend());
        }

        $this->_connections[$name] = $connection;
        return $connection;
    }

    /**
     * Create new connection adapter instance
     *
     * @param string $connAdapterName
     * @return Magento_Core_Model_Resource_ConnectionAdapterInterface|null
     */
    protected function _newConnection($connAdapterName)
    {
        $connection = null;
        // try to get connection adapter and create connection
        if ($connAdapterName) {
            $connectionAdapter = $this->_connAdapterFactory->create($connAdapterName);
            $connection = $connectionAdapter->getConnection();
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
            $tablePrefix = (string)$this->_tablePrefix;
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
     * Create new custom connection
     *
     * @param string $name
     * @param array $connectionConfig
     * @return Magento_DB_Adapter_Interface
     */
    public function createConnection($name, $connectionConfig)
    {
        if (!isset($this->_connections[$name])) {
            $connectionAdapter = $this->_connAdapterPool[$name]
                ? $this->_connAdapterPool[$name]
                : $this->_connAdapterPool[self::DEFAULT_SETUP_RESOURCE];

            $connection = $this->_connAdapterFactory->create($connectionAdapter, $connectionConfig);
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
