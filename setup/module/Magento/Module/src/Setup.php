<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Module;

use Magento\Module\Setup\Connection\AdapterInterface;
use Magento\Module\Setup\FileResolver as SetupFileResolver;
use Magento\Module\Updater\SetupInterface;

class Setup implements SetupInterface
{
    /**
     * Call afterApplyAllUpdates method flag
     *
     * @var boolean
     */
    protected $callAfterApplyAllUpdates = false;

    /**
     * Setup Connection
     *
     * @var \Magento\Framework\DB\Adapter\AdapterInterface
     */
    protected $connection = null;

    /**
     * Tables cache array
     *
     * @var array
     */
    protected $tables = array();

    /**
     * Tables data cache array
     *
     * @var array
     */
    protected $setupCache = array();

    /**
     * Filesystem instance
     *
     * @var \Magento\Filesystem\Filesystem
     */
    protected $filesystem;

    /**
     * Setup File Resolver
     *
     * @var SetupFileResolver
     */
    protected $setupFileResolver;

    /**
     * Table Prefix
     *
     * @var string
     */
    protected $tablePrefix;

    /**
     * Default Constructor
     *
     * @param AdapterInterface $connection
     * @param SetupFileResolver $setupFileResolver
     * @param array $connectionConfig
     * @return void
     */
    public function __construct(
        AdapterInterface $connection,
        SetupFileResolver $setupFileResolver,
        array $connectionConfig = array()
    ) {
        $this->connection = $connection->getConnection($connectionConfig);
        $this->setupFileResolver = $setupFileResolver;
    }

    /**
     * Get connection object
     *
     * @return \Magento\Framework\DB\Adapter\AdapterInterface
     */
    public function getConnection()
    {
        return $this->connection;
    }

    /**
     * Add table placeholder/table name relation
     *
     * @param string $tableName
     * @param string $realTableName
     * @return $this
     */
    public function setTable($tableName, $realTableName)
    {
        $this->tables[$tableName] = $realTableName;
        return $this;
    }

    /**
     * Get table name (validated by db adapter) by table placeholder
     *
     * @param string|array $tableName
     * @return string
     */
    public function getTable($tableName)
    {
        $tablePrefix = (string)$this->tablePrefix;
        if ($tablePrefix && strpos($tableName, $tablePrefix) !== 0) {
            $tableName = $tablePrefix . $tableName;
        }

        $cacheKey = $this->getTableCacheName($tableName);
        if (!isset($this->tables[$cacheKey])) {
            $this->tables[$cacheKey] = $this->connection->getTableName($tableName);
        }
        return $this->tables[$cacheKey];
    }

    /**
     * Retrieve table name for cache
     *
     * @param string|array $tableName
     * @return string
     */
    protected function getTableCacheName($tableName)
    {
        if (is_array($tableName)) {
            return join('_', $tableName);
        }
        return $tableName;
    }

    /**
     * Include file by path
     * This method should perform only file inclusion.
     * Implemented to prevent possibility of changing important and used variables
     * inside the setup model while installing
     *
     * @param string $fileName
     * @return mixed
     */
    protected function includeFile($fileName)
    {
        return include $fileName;
    }

    /**
     * Get data files for modifications
     *
     * @param string $actionType
     * @param string $fromVersion
     * @param string $toVersion
     * @param array $arrFiles
     * @return array
     */
    protected function getModifySqlFiles($actionType, $fromVersion, $toVersion, $arrFiles)
    {
        $arrRes = [];
        switch ($actionType) {
            case self::TYPE_DB_INSTALL:
            case self::TYPE_DATA_INSTALL:
                uksort($arrFiles, 'version_compare');
                foreach ($arrFiles as $version => $file) {
                    if (version_compare($version, $toVersion) !== self::VERSION_COMPARE_GREATER) {
                        $arrRes[0] = [
                            'toVersion' => $version,
                            'fileName'  => $file
                        ];
                    }
                }
                break;

            case self::TYPE_DB_UPGRADE:
            case self::TYPE_DATA_UPGRADE:
                uksort($arrFiles, 'version_compare');
                foreach ($arrFiles as $version => $file) {
                    $versionInfo = explode('-', $version);

                    // In array must be 2 elements: 0 => version from, 1 => version to
                    if (count($versionInfo) !== 2) {
                        break;
                    }
                    $infoFrom = $versionInfo[0];
                    $infoTo   = $versionInfo[1];
                    if (version_compare($infoFrom, $fromVersion, '>=')
                        && version_compare($infoTo, $fromVersion, '>')
                        && version_compare($infoTo, $toVersion, '<=')
                        && version_compare($infoFrom, $toVersion, '<')
                    ) {
                        $arrRes[] = [
                            'toVersion' => $infoTo,
                            'fileName'  => $file
                        ];
                    }
                }
                break;

            case self::TYPE_DB_ROLLBACK:
            case self::TYPE_DB_UNINSTALL:
            default:
                break;
        }
        return $arrRes;
    }

    /**
     * Apply data updates to the system after upgrading.
     *
     * @return $this
     */
    public function applyDataUpdates()
    {
        return $this;
    }

    /******************* UTILITY METHODS *****************/

    /**
     * Retrieve row or field from table by id or string and parent id
     *
     * @param string $table
     * @param string $idField
     * @param string|integer $rowId
     * @param string|null $field
     * @param string|null $parentField
     * @param string|integer $parentId
     * @return mixed
     */
    public function getTableRow($table, $idField, $rowId, $field = null, $parentField = null, $parentId = 0)
    {
        $table = $this->getTable($table);
        if (empty($this->setupCache[$table][$parentId][$rowId])) {
            $adapter = $this->getConnection();
            $bind = array('id_field' => $rowId);
            $select = $adapter->select()->from($table)->where($adapter->quoteIdentifier($idField) . '= :id_field');
            if (null !== $parentField) {
                $select->where($adapter->quoteIdentifier($parentField) . '= :parent_id');
                $bind['parent_id'] = $parentId;
            }
            $this->setupCache[$table][$parentId][$rowId] = $adapter->query($select, $bind);
        }

        if (null === $field) {
            return $this->setupCache[$table][$parentId][$rowId];
        }
        return isset(
        $this->setupCache[$table][$parentId][$rowId][$field]
        ) ? $this->setupCache[$table][$parentId][$rowId][$field] : false;
    }

    /**
     * Delete table row
     *
     * @param string $table
     * @param string $idField
     * @param string|int $rowId
     * @param null|string $parentField
     * @param int|string $parentId
     * @return $this
     */
    public function deleteTableRow($table, $idField, $rowId, $parentField = null, $parentId = 0)
    {
        $table = $this->getTable($table);
        $adapter = $this->getConnection();
        $where = array($adapter->quoteIdentifier($idField) . '=?' => $rowId);
        if (!is_null($parentField)) {
            $where[$adapter->quoteIdentifier($parentField) . '=?'] = $parentId;
        }

        $adapter->delete($table, $where);

        if (isset($this->setupCache[$table][$parentId][$rowId])) {
            unset($this->setupCache[$table][$parentId][$rowId]);
        }

        return $this;
    }

    /**
     * Update one or more fields of table row
     *
     * @param string $table
     * @param string $idField
     * @param string|integer $rowId
     * @param string|array $field
     * @param mixed|null $value
     * @param string $parentField
     * @param string|integer $parentId
     * @return $this
     */
    public function updateTableRow($table, $idField, $rowId, $field, $value = null, $parentField = null, $parentId = 0)
    {
        $table = $this->getTable($table);
        if (is_array($field)) {
            $data = $field;
        } else {
            $data = array($field => $value);
        }

        $adapter = $this->getConnection();
        $where = array($adapter->quoteIdentifier($idField) . '=?' => $rowId);
        $adapter->update($table, $data, $where);

        if (isset($this->setupCache[$table][$parentId][$rowId])) {
            if (is_array($field)) {
                $this->setupCache[$table][$parentId][$rowId] = array_merge(
                    $this->setupCache[$table][$parentId][$rowId],
                    $field
                );
            } else {
                $this->setupCache[$table][$parentId][$rowId][$field] = $value;
            }
        }

        return $this;
    }

    /**
     * Check is table exists
     *
     * @param string $table
     * @return bool
     */
    public function tableExists($table)
    {
        $table = $this->getTable($table);
        return $this->getConnection()->isTableExists($table);
    }

    /**
     * Prepare database before install/upgrade
     *
     * @return $this
     */
    public function startSetup()
    {
        $this->getConnection()->startSetup();
        return $this;
    }

    /**
     * Prepare database after install/upgrade
     *
     * @return $this
     */
    public function endSetup()
    {
        $this->getConnection()->endSetup();
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
    public function getIdxName($tableName, $fields, $indexType = '')
    {
        return $this->connection->getIndexName($tableName, $fields, $indexType);
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
        return $this->connection->getForeignKeyName($priTableName, $priColumnName, $refTableName, $refColumnName);
    }

    /**
     * Check call afterApplyAllUpdates method for setup class
     *
     * @return bool
     */
    public function getCallAfterApplyAllUpdates()
    {
        return $this->callAfterApplyAllUpdates;
    }

    /**
     * Run each time after applying of all updates,
     * if setup model setted $_callAfterApplyAllUpdates flag to true
     *
     * @return $this
     */
    public function afterApplyAllUpdates()
    {
        return $this;
    }

    /**
     * Add configuration data to core_config_data table
     *
     * @param string $key
     * @param string $value
     * @return void
     */
    public function addConfigData($key, $value)
    {
        $this->getConnection()->insert(
            $this->getTable('core_config_data'),
            array(
                'path'  => $key,
                'value' => $value
            ),
            true
        );
    }

    /**
     * Installs User Configuration Data
     *
     * @param array $data
     * @return void
     */
    public function installUserConfigurationData($data)
    {
        $this->addConfigData('web/seo/use_rewrites', $data['config']['rewrites']['allowed'], 0);
        $this->addConfigData('web/unsecure/base_url', $data['config']['address']['front'], '{{unsecure_base_url}}');
        $this->addConfigData('web/secure/use_in_frontend', $data['config']['https']['web'], 0);
        $this->addConfigData('web/secure/base_url', $data['config']['address']['front'], '{{secure_base_url}}');
        $this->addConfigData('web/secure/use_in_adminhtml', $data['config']['https']['admin'], 0);
        $this->addConfigData('general/locale/code', $data['store']['language'], 'en_US');
        $this->addConfigData('general/locale/timezone', $data['store']['timezone'], 'America/Los_Angeles');
        $this->addConfigData('currency/options/base', $data['store']['currency'], 'USD');
        $this->addConfigData('currency/options/default', $data['store']['currency'], 'USD');
        $this->addConfigData('currency/options/allow', $data['store']['currency'], 'USD');
    }
}
