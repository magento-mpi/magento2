<?php
/**
 * Module configuration file reader
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Core_Model_Config_Modules_Reader
{
    /**
     * Modules configuration
     *
     * @var Magento_Core_Model_Config_Modules
     */
    protected $_config;

    /**
     * Module file reader
     *
     * @var Magento_Core_Model_Config_Loader_Modules_File
     */
    protected $_fileReader;

    /**
     * @param Magento_Core_Model_Config_Modules $modulesConfig
     * @param Magento_Core_Model_Config_Loader_Modules_File $fileReader
     */
    public function __construct(
        Magento_Core_Model_Config_Modules $modulesConfig,
        Magento_Core_Model_Config_Loader_Modules_File $fileReader
    ) {
        $this->_config = $modulesConfig;
        $this->_fileReader = $fileReader;
    }

    /**
     * Iterate all active modules "etc" folders and combine data from
     * specidied xml file name to one object
     *
     * @param   string $fileName
     * @param   null|Magento_Core_Model_Config_Base $mergeToObject
     * @param   null|Magento_Core_Model_Config_Base $mergeModel
     * @return  Magento_Core_Model_Config_Base
     */
    public function loadModulesConfiguration($fileName, $mergeToObject = null, $mergeModel = null)
    {
        return $this->_fileReader->loadConfigurationFromFile($this->_config, $fileName, $mergeToObject, $mergeModel);
    }

    /**
     * Go through all modules and find configuration files of active modules
     *
     * @param string $filename
     * @return array
     */
    public function getModuleConfigurationFiles($filename)
    {
        return $this->_fileReader->getConfigurationFiles($this->_config, $filename);
    }

    /**
     * Get module directory by directory type
     *
     * @param   string $type
     * @param   string $moduleName
     * @return  string
     */
    public function getModuleDir($type, $moduleName)
    {
        return $this->_fileReader->getModuleDir($type, $moduleName);
    }

    /**
     * Set path to the corresponding module directory
     *
     * @param string $moduleName
     * @param string $type directory type (etc, controllers, locale etc)
     * @param string $path
     */
    public function setModuleDir($moduleName, $type, $path)
    {
        $this->_fileReader->setModuleDir($moduleName, $type, $path);
    }
}
