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
namespace Magento\Core\Model;

class ModuleManager
{
    /**
     * XPath in the configuration where module statuses are stored
     */
    const XML_PATH_MODULE_OUTPUT_STATUS = 'advanced/modules_disable_output/%s';

    /**
     * @var \Magento\Core\Model\Store\ConfigInterface
     */
    private $_storeConfig;

    /**
     * @var \Magento\Core\Model\ModuleListInterface
     */
    private $_moduleList;

    /**
     * @var array
     */
    private $_outputConfigPaths;

    /**
     * @param \Magento\Core\Model\Store\ConfigInterface $storeConfig
     * @param \Magento\Core\Model\ModuleListInterface $moduleList
     * @param array $outputConfigPaths
     */
    public function __construct(
        \Magento\Core\Model\Store\ConfigInterface $storeConfig,
        \Magento\Core\Model\ModuleListInterface $moduleList,
        array $outputConfigPaths = array()
    ) {
        $this->_storeConfig = $storeConfig;
        $this->_moduleList = $moduleList;
        $this->_outputConfigPaths = $outputConfigPaths;
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
        if ($this->_storeConfig->getConfigFlag(sprintf(self::XML_PATH_MODULE_OUTPUT_STATUS, $moduleName))) {
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
            return $this->_storeConfig->getConfigFlag($configPath);
        }
        return true;
    }
}
