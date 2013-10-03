<?php
/**
 * Resources and connections registry and factory
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Core\Model;

class Resource
{
    const AUTO_UPDATE_CACHE_KEY  = 'DB_AUTOUPDATE';
    const AUTO_UPDATE_ONCE       = 0;
    const AUTO_UPDATE_NEVER      = -1;
    const AUTO_UPDATE_ALWAYS     = 1;

    const PARAM_TABLE_PREFIX = 'db.table_prefix';

    /**
     * Instances of actual connections
     *
     * @var \Magento\DB\Adapter\Interface[]
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
     * @var \Magento\Core\Model\Config\ResourceInterface
     */
    protected $_resourceConfig;

    /**
     * Resource connection adapter factory
     *
     * @var \Magento\Core\Model\Resource\ConnectionFactory
     */
    protected $_connectionFactory;

    /**
     * Application cache
     *
     * @var \Magento\Core\Model\CacheInterface
     */
    protected $_cache;

    /**
     * @var string
     */
    protected $_tablePrefix;

    /**
     * @var \Magento\Core\Model\AppInterface
     */
    protected $_app;

    /**
     * @param \Magento\Core\Model\CacheInterface $cache
     * @param \Magento\Core\Model\AppInterface $app
     * @param \Magento\Core\Model\Config\ResourceInterface $resourceConfig
     * @param \Magento\Core\Model\Resource\ConnectionFactory $adapterFactory
     * @param string $tablePrefix
     */
    public function __construct(
        \Magento\Core\Model\CacheInterface $cache,
        \Magento\Core\Model\AppInterface $app,
        \Magento\Core\Model\Config\ResourceInterface $resourceConfig,
        \Magento\Core\Model\Resource\ConnectionFactory $adapterFactory,
        $tablePrefix = ''
    ) {
        $this->_cache = $cache;
        $this->_app = $app;
        $this->_resourceConfig = $resourceConfig;
        $this->_connectionFactory = $adapterFactory;
        $this->_tablePrefix = $tablePrefix;
    }

    /**
     * Set cache instance
     *
     * @param \Magento\Core\Model\CacheInterface $cache
     */
    public function setCache(\Magento\Core\Model\CacheInterface $cache)
    {
        $this->_cache = $cache;
    }

    /**
     * Retrieve connection adapter class name by connection type
     *
     * @param \Magento\Core\Model\Config\ResourceInterface $resourceConfig
     */
    public function setConfig(\Magento\Core\Model\Config\ResourceInterface $resourceConfig)
    {
        $this->_resourceConfig = $resourceConfig;
    }

    /**
     * Retrieve connection to resource specified by $resourceName
     *
     * @param string $resourceName
     * @return \Magento\DB\Adapter\AdapterInterface|bool
     */
    public function getConnection($resourceName)
    {
        $connectionName = $this->_resourceConfig->getConnectionName($resourceName);
        if (isset($this->_connections[$connectionName])) {
            return $this->_connections[$connectionName];
        }

        $connection = $this->_connectionFactory->create($connectionName);
        if (!$connection) {
            return false;
        }
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
        return $this->getConnection(\Magento\Core\Model\Config\Resource::DEFAULT_READ_CONNECTION)
            ->getTableName($tableName);
    }

    /**
     * Set mapped table name
     *
     * @param string $tableName
     * @param string $mappedName
     * @return \Magento\Core\Model\Resource
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
    public function getIdxName($tableName, $fields, $indexType = \Magento\DB\Adapter\AdapterInterface::INDEX_TYPE_INDEX)
    {
        return $this->getConnection(\Magento\Core\Model\Config\Resource::DEFAULT_READ_CONNECTION)
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
        return $this->getConnection(\Magento\Core\Model\Config\Resource::DEFAULT_READ_CONNECTION)
            ->getForeignKeyName($this->getTableName($priTableName), $priColumnName,
                $this->getTableName($refTableName), $refColumnName);
    }
}
