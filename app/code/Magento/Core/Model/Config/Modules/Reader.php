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
     * Module directories that were set explicitly
     *
     * @var array
     */
    protected $_customModuleDirs = array();

    /**
     * Directory registry
     *
     * @var Magento_Core_Model_Module_Dir
     */
    protected $_moduleDirs;

    /**
     * Modules configuration provider
     *
     * @var Magento_Core_Model_ModuleListInterface
     */
    protected $_modulesList;

    /**
     * Base config factory
     *
     * @var Magento_Core_Model_Config_BaseFactory
     */
    protected $_prototypeFactory;

    /**
     * @param Magento_Core_Model_Module_Dir $moduleDirs
     * @param Magento_Core_Model_Config_BaseFactory $prototypeFactory
     * @param Magento_Core_Model_ModuleListInterface $moduleList
     */
    public function __construct(
        Magento_Core_Model_Module_Dir $moduleDirs,
        Magento_Core_Model_Config_BaseFactory $prototypeFactory,
        Magento_Core_Model_ModuleListInterface $moduleList
    ) {
        $this->_moduleDirs = $moduleDirs;
        $this->_prototypeFactory = $prototypeFactory;
        $this->_modulesList = $moduleList;
    }

    /**
     * Load configuration from single file
     *
     * @param string $configFile
     * @param string $moduleName
     * @param Magento_Core_Model_Config_Base $mergeToObject
     * @param Magento_Core_Model_Config_Base $mergeModel
     */
    public function _loadFileConfig($configFile, $moduleName, $mergeToObject, $mergeModel)
    {
        $configFilePath = $this->getModuleDir('etc', $moduleName) . DS . $configFile;
        if ($mergeModel->loadFile($configFilePath)) {
            $mergeToObject->extend($mergeModel, true);
        }
    }

    /**
     * Iterate all active modules "etc" folders and combine data from
     * specified xml file name to one object
     *
     * @param string $fileName
     * @param Magento_Core_Model_Config_Base|null $mergeToObject
     * @param Magento_Core_Model_Config_Base|null $mergeModel
     * @return Magento_Core_Model_Config_Base|null
     */
    public function loadModulesConfiguration($fileName, $mergeToObject = null, $mergeModel = null)
    {
        $mergeToObject = null === $mergeToObject ? $this->_prototypeFactory->create('<config/>') : $mergeToObject;
        $mergeModel = null === $mergeModel ? $mergeModel = $this->_prototypeFactory->create('<config/>'): $mergeModel;

        /** @var $module Magento_Core_Model_Config_Element */
        foreach (array_keys($this->_modulesList->getModules()) as $moduleName) {
            if (!is_array($fileName)) {
                $fileName = array($fileName);
            }
            foreach ($fileName as $configFile) {
                $this->_loadFileConfig($configFile, $moduleName, $mergeToObject, $mergeModel);
            }

        }
        return $mergeToObject;
    }

    /**
     * Go through all modules and find configuration files of active modules
     *
     * @param $filename
     * @return array
     */
    public function getConfigurationFiles($filename)
    {
        $result = array();
        foreach (array_keys($this->_modulesList->getModules()) as $moduleName) {
            $file = $this->getModuleDir('etc', $moduleName) . DIRECTORY_SEPARATOR . $filename;
            if (file_exists($file)) {
                $result[] = $file;
            }
        }
        return $result;
    }

    /**
     * Get module directory by directory type
     *
     * @param string $type
     * @param string $moduleName
     * @return string
     */
    public function getModuleDir($type, $moduleName)
    {
        if (isset($this->_customModuleDirs[$moduleName][$type])) {
            return $this->_customModuleDirs[$moduleName][$type];
        }
        return $this->_moduleDirs->getDir($moduleName, $type);
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
        $this->_customModuleDirs[$moduleName][$type] = $path;
    }
}
