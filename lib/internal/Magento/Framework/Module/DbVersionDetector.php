<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Module;

use Magento\Framework\Module\Updater\SetupInterface;

/**
 * Class DbVersionDetector
 *
 */
class DbVersionDetector
{
    /**#@+
     * Constants defined for keys of error array
     */
    const ERROR_KEY_MODULE = 'module';
    const ERROR_KEY_TYPE = 'type';
    const ERROR_KEY_CURRENT = 'current';
    const ERROR_KEY_REQUIRED = 'required';
    /**#@-*/

    /**
     * @var ModuleListInterface
     */
    private $moduleList;

    /**
     * @var ResourceInterface
     */
    private $moduleResource;

    /**
     * @var ResourceResolverInterface
     */
    private $resourceResolver;

    /**
     * @param ModuleListInterface $moduleList
     * @param ResourceInterface $moduleResource
     * @param ResourceResolverInterface $resourceResolver
     */
    public function __construct(
        ModuleListInterface $moduleList,
        ResourceInterface $moduleResource,
        ResourceResolverInterface $resourceResolver
    ) {
        $this->moduleList = $moduleList;
        $this->moduleResource = $moduleResource;
        $this->resourceResolver = $resourceResolver;
    }

    /**
     * Check if DB schema is up to date
     *
     * @param string $moduleName
     * @param string $resourceName
     * @return bool
     */
    public function isDbSchemaUpToDate($moduleName, $resourceName)
    {
        $dbVer = $this->moduleResource->getDbVersion($resourceName);
        return $this->isModuleVersionEqual($moduleName, $dbVer);
    }

    /**
     * @param string $moduleName
     * @param string $resourceName
     * @return bool
     */
    public function isDbDataUpToDate($moduleName, $resourceName)
    {
        $dataVer = $this->moduleResource->getDataVersion($resourceName);
        return $this->isModuleVersionEqual($moduleName, $dataVer);
    }

    /**
     * Get array of errors if DB is out of date, return [] if DB is current
     *
     * @return [] Array of errors, each error contains module name, current version, needed version,
     *              and type (schema or data).  The array will be empty if all schema and data are current.
     */
    public function getDbVersionErrors()
    {
        $errors = [];
        foreach ($this->moduleList->getNames() as $moduleName) {
            foreach ($this->resourceResolver->getResourceList($moduleName) as $resourceName) {
                if (!$this->isDbSchemaUpToDate($moduleName, $resourceName)) {
                    $errors[] = $this->getDbSchemaVersionError($moduleName, $resourceName);
                }

                if (!$this->isDbDataUpToDate($moduleName, $resourceName)) {
                    $errors[] = $this->getDbDataVersionError($moduleName, $resourceName);
                }
            }
        }
        return $errors;
    }

    /**
     * Check if DB schema is up to date, return error data if it is not.
     *
     * @param string $moduleName
     * @param string $resourceName
     * @return [] Contains current and needed version strings
     */
    private function getDbSchemaVersionError($moduleName, $resourceName)
    {

        $dbVer = $this->moduleResource->getDbVersion($resourceName); // version saved in DB
        $module = $this->moduleList->getOne($moduleName);
        $configVer = $module['schema_version'];
        $dbVer = $dbVer ?: 'none';
        return [
            self::ERROR_KEY_CURRENT => $dbVer,
            self::ERROR_KEY_REQUIRED => $configVer,
            self::ERROR_KEY_MODULE => $moduleName,
            self::ERROR_KEY_TYPE => 'schema'
        ];
    }

    /**
     * Get error data for an out-of-date schema or data.
     *
     * @param string $moduleName
     * @param string $resourceName
     * @return []
     */
    private function getDbDataVersionError($moduleName, $resourceName)
    {
        $dataVer = $this->moduleResource->getDataVersion($resourceName);
        $module = $this->moduleList->getOne($moduleName);
        $configVer = $module['schema_version'];
        $dataVer = $dataVer ?: 'none';
        return [
            self::ERROR_KEY_CURRENT => $dataVer,
            self::ERROR_KEY_REQUIRED => $configVer,
            self::ERROR_KEY_MODULE => $moduleName,
            self::ERROR_KEY_TYPE => 'data'
        ];
    }

    /**
     * Check if DB data is up to date
     *
     * @param string $moduleName
     * @param string|bool $version
     * @return bool
     * @throws \UnexpectedValueException
     */
    private function isModuleVersionEqual($moduleName, $version)
    {
        $module = $this->moduleList->getOne($moduleName);
        if (empty($module['schema_version'])) {
            throw new \UnexpectedValueException("Schema version for module '$moduleName' is not specified");
        }
        $configVer = $module['schema_version'];

        return ($version !== false
            && version_compare($configVer, $version) === SetupInterface::VERSION_COMPARE_EQUAL);
    }
}
