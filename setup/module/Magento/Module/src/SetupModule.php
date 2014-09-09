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
use Magento\Module\Resource\Resource;

class SetupModule extends Setup
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
     * Resource
     *
     * @var ResourceInterface
     */
    protected $resource;

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
     * @param ModuleListInterface $moduleList
     * @param SetupFileResolver $setupFileResolver
     * @param string $moduleName
     * @param array $connectionConfig
     * @return void
     */
    public function __construct(
        AdapterInterface $connection,
        ModuleListInterface $moduleList,
        SetupFileResolver $setupFileResolver,
        $moduleName,
        array $connectionConfig = array()
    ) {
        parent::__construct($connection, $setupFileResolver, $connectionConfig);
        $this->moduleConfig = $moduleList->getModule($moduleName);
        $this->resource = new Resource($this->connection);
        $this->resourceName = $this->setupFileResolver->getResourceCode($moduleName);
    }

    /**
     * Apply module recurring post schema updates
     *
     * @return $this
     * @throws \Exception
     */
    public function applyRecurringUpdates()
    {
        $moduleName = (string)$this->moduleConfig['name'];
        foreach ($this->setupFileResolver->getSqlSetupFiles($moduleName, self::TYPE_DB_RECURRING . '.php') as $file) {
            try {
                $file = $this->setupFileResolver->getAbsolutePath($file);
                $this->includeFile($file);
            } catch (\Exception $e) {
                throw new \Exception(sprintf('Error in file: "%s" - %s', $file, $e->getMessage()), 0, $e);
            }
        }
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
    protected function getAvailableDbFiles($actionType, $fromVersion, $toVersion)
    {
        $moduleName = (string)$this->moduleConfig['name'];
        $dbFiles = array();
        $typeFiles = array();
        $regExpDb = sprintf('#%s-(.*)\.(php|sql)$#i', $actionType);
        $regExpType = sprintf('#%s-%s-(.*)\.(php|sql)$#i', 'mysql4', $actionType);
        foreach ($this->setupFileResolver->getSqlSetupFiles($moduleName, '*.{php,sql}') as $file) {
            $matches = array();
            if (preg_match($regExpDb, $file, $matches)) {
                $dbFiles[$matches[1]] = $this->setupFileResolver->getAbsolutePath($file);
            } elseif (preg_match($regExpType, $file, $matches)) {
                $typeFiles[$matches[1]] = $this->setupFileResolver->getAbsolutePath($file);
            }
        }

        if (empty($typeFiles) && empty($dbFiles)) {
            return array();
        }

        foreach ($typeFiles as $version => $file) {
            $dbFiles[$version] = $file;
        }

        return $this->getModifySqlFiles($actionType, $fromVersion, $toVersion, $dbFiles);
    }

    /**
     * Apply module resource install, upgrade and data scripts
     *
     * @return $this|true
     */
    public function applyUpdates()
    {
        if (!$this->resourceName) {
            return $this;
        }
        $dbVer = $this->resource->getDbVersion($this->resourceName);
        $configVer = $this->moduleConfig['schema_version'];

        // Module is installed
        if ($dbVer !== false) {
            $status = version_compare($configVer, $dbVer);
            switch ($status) {
                case self::VERSION_COMPARE_LOWER:
                    $this->rollbackResourceDb($configVer, $dbVer);
                    break;
                case self::VERSION_COMPARE_GREATER:
                    $this->upgradeResourceDb($dbVer, $configVer);
                    break;
                default:
                    return true;
                    break;
            }
        } elseif ($configVer) {
            $this->installResourceDb($configVer);
        }
        return $this;
    }

    /**
     * Run resource installation file
     *
     * @param string $newVersion
     * @return $this
     */
    protected function installResourceDb($newVersion)
    {
        $oldVersion = $this->modifyResourceDb(self::TYPE_DB_INSTALL, '', $newVersion);
        $this->modifyResourceDb(self::TYPE_DB_UPGRADE, $oldVersion, $newVersion);
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
    protected function upgradeResourceDb($oldVersion, $newVersion)
    {
        $this->modifyResourceDb(self::TYPE_DB_UPGRADE, $oldVersion, $newVersion);
        $this->resource->setDbVersion($this->resourceName, $newVersion);

        return $this;
    }

    /**
     * Save resource version
     *
     * @param string $actionType
     * @param string $version
     * @return $this
     */
    protected function setResourceVersion($actionType, $version)
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
    protected function modifyResourceDb($actionType, $fromVersion, $toVersion)
    {
        switch ($actionType) {
            case self::TYPE_DB_INSTALL:
            case self::TYPE_DB_UPGRADE:
                $files = $this->getAvailableDbFiles($actionType, $fromVersion, $toVersion);
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
                        $result = $this->includeFile($fileName);
                        break;
                    default:
                        $result = false;
                        break;
                }

                if ($result) {
                    $this->setResourceVersion($actionType, $file['toVersion']);
                    //@todo log
                } else {
                    //@todo log "Failed resource setup: {$fileName}";
                }
            } catch (\Exception $e) {
                throw new \Exception(sprintf('Error in file: "%s" - %s', $fileName, $e->getMessage()), 0, $e);
            }
            $version = $file['toVersion'];
        }
        return $version;
    }

    /**
     * Roll back resource
     *
     * @param string $newVersion
     * @param string $oldVersion
     * @return $this
     */
    protected function rollbackResourceDb($newVersion, $oldVersion)
    {
        $this->modifyResourceDb(self::TYPE_DB_ROLLBACK, $newVersion, $oldVersion);
        return $this;
    }

    /**
     * Uninstall resource
     *
     * @param string $version existing resource version
     * @return $this
     */
    protected function uninstallResourceDb($version)
    {
        $this->modifyResourceDb(self::TYPE_DB_UNINSTALL, $version, '');
        return $this;
    }

    /**
     * Set table prefix
     *
     * @param string $tablePrefix
     * @return void
     */
    public function setTablePrefix($tablePrefix)
    {
        $this->tablePrefix = $tablePrefix;
        $this->resource->setTablePrefix($this->tablePrefix);
    }
}
