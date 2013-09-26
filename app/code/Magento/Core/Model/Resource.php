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

    const PARAM_TABLE_PREFIX = 'db.table_prefix';



    /**
     * Instances of actual connections
     *
     * @var Magento_DB_Adapter_Interface[]
     */
    protected $_connections = array();

    /**
     * Mapped tables cache array
     *
     * @var array
     */
    protected $_mappedTableNames;

    /**
     * Resource config
     *
     * @var Magento_Core_Model_Config_Resource
     */
    protected $_resourceConfig;

    /**
     * Resource connection adapter factory
     *
     * @var Magento_Core_Model_ConnectionAdapterFactory
     */
    protected $_adapterFactory;

    /**
     * Application cache
     *
     * @var Magento_Core_Model_CacheInterface
     */
    protected $_cache;

    /**
     * @var string
     */
    protected $_tablePrefix;

    /**
     * @param Magento_Core_Model_CacheInterface $cache
     * @param Magento_Core_Model_Config_Resource $resourceConfig
     * @param Magento_Core_Model_ConnectionAdapterFactory $adapterFactory
     * @param string $tablePrefix
     */
    public function __construct(
        Magento_Core_Model_CacheInterface $cache,
        Magento_Core_Model_Config_Resource $resourceConfig,
        Magento_Core_Model_ConnectionAdapterFactory $adapterFactory,
        $tablePrefix = ''
    ) {
        $this->_resourceConfig = $resourceConfig;
        $this->_adapterFactory = $adapterFactory;
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
     * Retrieve connection to resource specified by $resourceName
     *
     * @param string $resourceName
     * @return Magento_DB_Adapter_Interface|bool
     */
    public function getConnection($resourceName)
    {
        $connectionName = $this->_resourceConfig->getConnectionName($resourceName);
        if (isset($this->_connections[$connectionName])) {
            return $this->_connections[$connectionName];
        }

        $adapter = $this->_adapterFactory->create($connectionName);

        $connection = $adapter->getConnection();
        $connection->setCacheAdapter($this->_cache->getFrontend());

        $this->_connections[$connectionName] = $connection;
        return $connection;
    }

    /**
     * Retrieve default connection name by required connection name
     *
     * @param string $requiredConnectionName
     * @return string
     */
    protected function _getDefaultResourceName($requiredConnectionName)
    {
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
        return $this->getConnection(Magento_Core_Model_Config_Resource::DEFAULT_READ_RESOURCE)
            ->getTableName($tableName);
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
        return $this->getConnection(Magento_Core_Model_Config_Resource::DEFAULT_READ_RESOURCE)
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
        return $this->getConnection(Magento_Core_Model_Config_Resource::DEFAULT_READ_RESOURCE)
            ->getForeignKeyName($this->getTableName($priTableName), $priColumnName,
                $this->getTableName($refTableName), $refColumnName);
    }
}
