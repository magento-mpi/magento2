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
class Mage_Core_Model_ModuleManager
{
    /**
     * @var Mage_Core_Model_ConfigInterface
     */
    private $_config;

    /**
     * @var Mage_Core_Model_Store_ConfigInterface
     */
    private $_storeConfig;

    /**
     * @var array
     */
    private $_outputConfigPaths;

    /**
     * @param Mage_Core_Model_ConfigInterface $config
     * @param Mage_Core_Model_Store_ConfigInterface $storeConfig
     * @param array $outputConfigPaths Format: array('<Module_Name>' => '<store_config_path>', ...)
     */
    public function __construct(
        Mage_Core_Model_ConfigInterface $config,
        Mage_Core_Model_Store_ConfigInterface $storeConfig,
        array $outputConfigPaths = array()
    ) {
        $this->_config = $config;
        $this->_storeConfig = $storeConfig;
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
        if (!$this->_config->getNode("modules/$moduleName")) {
            return false;
        }
        $moduleStatus = $this->_config->getNode("modules/$moduleName/active");
        if (!$moduleStatus || !in_array((string)$moduleStatus, array('true', '1'))) {
            return false;
        }
        return true;
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
        if ($this->_storeConfig->getConfigFlag("advanced/modules_disable_output/$moduleName")) {
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
