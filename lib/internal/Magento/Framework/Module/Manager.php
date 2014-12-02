<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Module statuses manager
 */
namespace Magento\Framework\Module;

use Magento\Framework\Module\Plugin\DbStatusValidator;
use Magento\Framework\Module\Updater\SetupInterface;

class Manager
{
    /**
     * @var Output\ConfigInterface
     */
    private $_outputConfig;

    /**
     * @var ModuleListInterface
     */
    private $_moduleList;

    /**
     * @var array
     */
    private $_outputConfigPaths;

    /**
     * @var ResourceInterface
     */
    private $_moduleResource;

    /**
     * @param Output\ConfigInterface $outputConfig
     * @param ModuleListInterface $moduleList
     * @param ResourceInterface $moduleResource
     * @param array $outputConfigPaths
     */
    public function __construct(
        Output\ConfigInterface $outputConfig,
        ModuleListInterface $moduleList,
        ResourceInterface $moduleResource,
        array $outputConfigPaths = array()
    ) {
        $this->_outputConfig = $outputConfig;
        $this->_moduleList = $moduleList;
        $this->_outputConfigPaths = $outputConfigPaths;
        $this->_moduleResource = $moduleResource;
    }

    /**
     * Whether a module is enabled in the configuration or not
     *
     * @param string $moduleName Fully-qualified module name
     * @return boolean
     */
    public function isEnabled($moduleName)
    {
        return !!$this->_moduleList->getModule($moduleName);
    }

    /**
     * Whether a module output is permitted by the configuration or not
     *
     * @param string $moduleName Fully-qualified module name
     * @return boolean
     */
    public function isOutputEnabled($moduleName)
    {
        if (!$this->isEnabled($moduleName)) {
            return false;
        }
        if (!$this->_isCustomOutputConfigEnabled($moduleName)) {
            return false;
        }
        if ($this->_outputConfig->isEnabled($moduleName)) {
            return false;
        }
        return true;
    }

    /**
     * Whether a configuration switch for a module output permits output or not
     *
     * @param string $moduleName Fully-qualified module name
     * @return boolean
     */
    protected function _isCustomOutputConfigEnabled($moduleName)
    {
        if (isset($this->_outputConfigPaths[$moduleName])) {
            $configPath = $this->_outputConfigPaths[$moduleName];
            if (defined($configPath)) {
                $configPath = constant($configPath);
            }
            return $this->_outputConfig->isSetFlag($configPath);
        }
        return true;
    }

    /**
     * Check if DB schema is up to date, return error data if it is not.
     *
     * @param string $moduleName
     * @param string $resourceName
     * @return [] Contains current and needed version strings
     */
    public function getDbSchemaVersionError($moduleName, $resourceName)
    {
        $dbVer = $this->_moduleResource->getDbVersion($resourceName); // version saved in DB

        $configVer = $this->verifyModuleVersion($moduleName, $dbVer);

        if ($configVer === true) {
            return [];
        } else {
            $dbVer = $dbVer ?: 'none';
            return [DbStatusValidator::ERROR_KEY_CURRENT => $dbVer, DbStatusValidator::ERROR_KEY_NEEDED => $configVer];
        }
    }

    /**
     * Check if DB data is up to date, return error data if it is not.
     *
     * @param string $moduleName
     * @param string $resourceName
     * @return []
     */
    public function getDbDataVersionError($moduleName, $resourceName)
    {
        $dataVer = $this->_moduleResource->getDataVersion($resourceName);
        $configVer = $this->verifyModuleVersion($moduleName, $dataVer);
        if ($configVer === true) {
            return [];
        } else {
            $dataVer = $dataVer ?: 'none';
            return [
                DbStatusValidator::ERROR_KEY_CURRENT => $dataVer,
                DbStatusValidator::ERROR_KEY_NEEDED => $configVer
            ];
        }
    }

    /**
     * Check if DB data is up to date
     *
     * @param string $moduleName
     * @param string|bool $version
     * @return true|string Returns true if up to date, string containing current version if it is not.
     * @throws \UnexpectedValueException
     */
    private function verifyModuleVersion($moduleName, $version)
    {
        $module = $this->_moduleList->getModule($moduleName);
        if (empty($module['schema_version'])) {
            throw new \UnexpectedValueException("Schema version for module '$moduleName' is not specified");
        }
        $configVer = $module['schema_version'];

        $compareResult = ($version !== false
            && version_compare($configVer, $version) === SetupInterface::VERSION_COMPARE_EQUAL);

        if ($compareResult) {
            return true;
        } else {
            return $configVer;
        }
    }
}
