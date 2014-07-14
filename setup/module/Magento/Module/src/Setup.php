<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Module;

use Magento\Module\Setup\ConnectionAdapterInterface;
use Magento\Module\Updater\SetupInterface;

class Setup implements SetupInterface
{
    /**
     * Setup resource name
     * @var string
     */
    protected $resourceName;

    /**
     * Setup module configuration object
     *
     * @var array
     */
    protected $moduleConfig;

    /**
     * Call afterApplyAllUpdates method flag
     *
     * @var boolean
     */
    protected $callAfterApplyAllUpdates = false;

    /**
     * Setup Connection
     *
     * @var \Magento\Db\Adapter\AdapterInterface
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
     * Modules configuration
     *
     * @var \Magento\Framework\App\Resource
     */
    protected $resourceModel;

    /**
     * Modules configuration reader
     *
     * @var \Magento\Framework\Module\Dir\Reader
     */
    protected $modulesReader;

    /**
     * @var \Magento\Framework\Event\ManagerInterface
     */
    protected $eventManager;

    /**
     * @var \Magento\Framework\Logger
     */
    protected $logger;

    /**
     * @var ResourceInterface
     */
    protected $resource;

    /**
     * Filesystem instance
     *
     * @var \Magento\Filesystem\Filesystem
     */
    protected $filesystem;

    /**
     * @var \Magento\Framework\Filesystem\Directory\ReadInterface
     */
    protected $modulesDir;

    /**
     * @var Dir
     */
    protected $dir;

    /**
     * @param ConnectionAdapterInterface $connection
     * @param ModuleListInterface $moduleList
     * @param ResourceInterface $resource
     * @param Dir $dir
     * @param $resourceName
     * @param $moduleName
     */
    public function __construct(
        ConnectionAdapterInterface $connection,
        ModuleListInterface $moduleList,
        ResourceInterface $resource,
        Dir $dir,
        $resourceName,
        $moduleName
    ) {
        $this->connection = $connection->getConnection();
        $this->moduleConfig = $moduleList->getModule($moduleName);
        $this->resource = $resource;
        $this->dir = $dir;
        $this->resourceName = $resourceName;
        /**$this->_eventManager = $context->getEventManager();
        $this->_resourceModel = $context->getResourceModel();
        $this->_logger = $context->getLogger();
        $this->_modulesReader = $context->getModulesReader();
        $this->_moduleConfig = $context->getModuleList()->getModule($moduleName);
        $this->filesystem = $context->getFilesystem();
        $this->modulesDir = $this->filesystem->getDirectoryRead(\Magento\Framework\App\Filesystem::MODULES_DIR);*/
    }

    /**
     * Get connection object
     *
     * @return \Magento\Db\Adapter\AdapterInterface
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
     * Apply data updates to the system after upgrading.
     *
     * @return $this
     */
    public function applyDataUpdates()
    {
        return $this;
    }

    /**
     * Apply module resource install, upgrade and data scripts
     *
     * @return $this|true
     */
    public function applyUpdates()
    {
        $dbVer = $this->resource->getDbVersion($this->resourceName);
        $configVer = $this->moduleConfig['schema_version'];

        // Module is installed
        if ($dbVer !== false) {
            $status = version_compare($configVer, $dbVer);
            switch ($status) {
                case self::VERSION_COMPARE_LOWER:
                    $this->_rollbackResourceDb($configVer, $dbVer);
                    break;
                case self::VERSION_COMPARE_GREATER:
                    $this->_upgradeResourceDb($dbVer, $configVer);
                    break;
                default:
                    return true;
                    break;
            }
        } elseif ($configVer) {
            $this->_installResourceDb($configVer);
        }

        return $this;
    }

    /**
     * Run resource installation file
     *
     * @param string $newVersion
     * @return $this
     */
    protected function _installResourceDb($newVersion)
    {
        $oldVersion = $this->_modifyResourceDb(self::TYPE_DB_INSTALL, '', $newVersion);
        $this->_modifyResourceDb(self::TYPE_DB_UPGRADE, $oldVersion, $newVersion);
        $this->resource->setDbVersion($this->resourceName, $newVersion);

        return $this;
    }

    /**
     * Run resource upgrade files from $oldVersion to $newVersion
     *
     * @param string $oldVersion
     * @param string $newVersion
     * @return $this
     */
    protected function _upgradeResourceDb($oldVersion, $newVersion)
    {
        $this->_modifyResourceDb(self::TYPE_DB_UPGRADE, $oldVersion, $newVersion);
        $this->resource->setDbVersion($this->resourceName, $newVersion);

        return $this;
    }

    /**
     * Roll back resource
     *
     * @param string $newVersion
     * @param string $oldVersion
     * @return $this
     */
    protected function _rollbackResourceDb($newVersion, $oldVersion)
    {
        $this->_modifyResourceDb(self::TYPE_DB_ROLLBACK, $newVersion, $oldVersion);
        return $this;
    }

    /**
     * Uninstall resource
     *
     * @param string $version existing resource version
     * @return $this
     */
    protected function _uninstallResourceDb($version)
    {
        $this->_modifyResourceDb(self::TYPE_DB_UNINSTALL, $version, '');
        return $this;
    }

    /**
     * Retrieve available Database install/upgrade files for current module
     *
     * @param string $actionType
     * @param string $fromVersion
     * @param string $toVersion
     * @return array
     */
    protected function _getAvailableDbFiles($actionType, $fromVersion, $toVersion)
    {
        $modName = (string)$this->moduleConfig['name'];

        $filesDir = $this->dir->getDir($modName, 'sql') . '/' . $this->resourceName;
        $modulesDirPath = $this->modulesDir->getRelativePath($filesDir);
        if (!$this->modulesDir->isDirectory($modulesDirPath) || !$this->modulesDir->isReadable($modulesDirPath)) {
            return array();
        }

        $dbFiles = array();
        $typeFiles = array();
        $regExpDb = sprintf('#%s-(.*)\.(php|sql)$#i', $actionType);
        $regExpType = sprintf('#%s-%s-(.*)\.(php|sql)$#i', 'mysql4', $actionType);
        foreach ($this->modulesDir->read($modulesDirPath) as $file) {
            $matches = array();
            if (preg_match($regExpDb, $file, $matches)) {
                $dbFiles[$matches[1]] = $this->modulesDir->getAbsolutePath($file);
            } else if (preg_match($regExpType, $file, $matches)) {
                $typeFiles[$matches[1]] = $this->modulesDir->getAbsolutePath($file);
            }
        }

        if (empty($typeFiles) && empty($dbFiles)) {
            return array();
        }

        foreach ($typeFiles as $version => $file) {
            $dbFiles[$version] = $file;
        }

        return $this->_getModifySqlFiles($actionType, $fromVersion, $toVersion, $dbFiles);
    }

    /**
     * Save resource version
     *
     * @param string $actionType
     * @param string $version
     * @return $this
     */
    protected function _setResourceVersion($actionType, $version)
    {
        switch ($actionType) {
            case self::TYPE_DB_INSTALL:
            case self::TYPE_DB_UPGRADE:
                $this->resource->setDbVersion($this->resourceName, $version);
                break;
            case self::TYPE_DATA_INSTALL:
            case self::TYPE_DATA_UPGRADE:
            default:
                break;
        }

        return $this;
    }

    /**
     * Run module modification files. Return version of last applied upgrade (false if no upgrades applied)
     * @param string $actionType
     * @param string $fromVersion
     * @param string $toVersion
     * @return false|string
     * @throws \Exception
     */
    protected function _modifyResourceDb($actionType, $fromVersion, $toVersion)
    {
        switch ($actionType) {
            case self::TYPE_DB_INSTALL:
            case self::TYPE_DB_UPGRADE:
                $files = $this->_getAvailableDbFiles($actionType, $fromVersion, $toVersion);
                break;
            case self::TYPE_DATA_INSTALL:
            case self::TYPE_DATA_UPGRADE:
                break;
            default:
                $files = array();
                break;
        }
        if (empty($files) || !$this->getConnection()) {
            return false;
        }

        $version = false;

        foreach ($files as $file) {
            $fileName = $file['fileName'];
            $fileType = pathinfo($fileName, PATHINFO_EXTENSION);
            try {
                switch ($fileType) {
                    case 'php':
                        $result = $this->_includeFile($fileName);
                        break;
                    default:
                        $result = false;
                        break;
                }

                if ($result) {
                    $this->_setResourceVersion($actionType, $file['toVersion']);
//                    $this->_logger->log($fileName); @todo: logger
                } else {
//                    $this->_logger->log("Failed resource setup: {$fileName}"); @todo: logger
                }
            } catch (\Exception $e) {
                throw new \Exception(sprintf('Error in file: "%s" - %s', $fileName, $e->getMessage()), 0, $e);
            }
            $version = $file['toVersion'];
        }
        return $version;
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
    protected function _includeFile($fileName)
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
    protected function _getModifySqlFiles($actionType, $fromVersion, $toVersion, $arrFiles)
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
     * @return \Magento\Filesystem\Filesystem
     */
    public function getFilesystem()
    {
        return $this->filesystem;
    }
}
