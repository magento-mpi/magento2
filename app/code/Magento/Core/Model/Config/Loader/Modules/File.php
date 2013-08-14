<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Core_Model_Config_Loader_Modules_File
{
    /**
     * Modules configuration
     *
     * @var Magento_Core_Model_Config_Modules
     */
    protected $_modulesConfig;

    /**
     * Base config factory
     *
     * @var Magento_Core_Model_Config_BaseFactory
     */
    protected $_prototypeFactory;

    /**
     * Module configuration directories
     *
     * @var array
     */
    protected $_moduleDirs = array();

    /**
     * Directory registry
     *
     * @var Magento_Core_Model_Dir
     */
    protected $_dirs;

    /**
     * @param Magento_Core_Model_Dir $dirs
     * @param Magento_Core_Model_Config_BaseFactory $prototypeFactory
     */
    public function __construct(
        Magento_Core_Model_Dir $dirs,
        Magento_Core_Model_Config_BaseFactory $prototypeFactory
    ) {
        $this->_dirs = $dirs;
        $this->_prototypeFactory = $prototypeFactory;
    }

    /**
     * Iterate all active modules "etc" folders and combine data from
     * specidied xml file name to one object
     *
     * @param Magento_Core_Model_ConfigInterface $modulesConfig
     * @param string $fileName
     * @param Magento_Core_Model_Config_Base|null $mergeToObject
     * @param Magento_Core_Model_Config_Base|null $mergeModel
     * @param array $configCache
     * @return Magento_Core_Model_Config_Base|null
     */
    public function loadConfigurationFromFile(
        Magento_Core_Model_ConfigInterface $modulesConfig,
        $fileName,
        $mergeToObject = null,
        $mergeModel = null,
        $configCache = array()
    ) {
        $mergeToObject = null === $mergeToObject ? $this->_prototypeFactory->create('<config/>') : $mergeToObject;
        $mergeModel = null === $mergeModel ? $mergeModel = $this->_prototypeFactory->create('<config/>'): $mergeModel;

        $modules = $modulesConfig->getNode('modules')->children();
        /** @var $module Magento_Core_Model_Config_Element */
        foreach ($modules as $modName => $module) {
            if ($module->is('active')) {
                if (!is_array($fileName)) {
                    $fileName = array($fileName);
                }
                foreach ($fileName as $configFile) {
                    $this->_loadFileConfig($configFile, $configCache, $modName, $mergeToObject, $mergeModel);
                }
            }
        }
        return $mergeToObject;
    }

    /**
     * Load configuration from single file
     *
     * @param string $configFile
     * @param array $configCache
     * @param string $modName
     * @param Magento_Core_Model_Config_Base $mergeToObject
     * @param Magento_Core_Model_Config_Base $mergeModel
     */
    public function _loadFileConfig($configFile, $configCache, $modName, $mergeToObject, $mergeModel)
    {
        if ($configFile == 'config.xml' && isset($configCache[$modName])) {
            $mergeToObject->extend($configCache[$modName], true);
            //Prevent overriding <active> node of module if it was redefined in etc/modules
            $mergeToObject->extend(
                $this->_prototypeFactory->create(
                    "<config><modules><{$modName}><active>true</active></{$modName}></modules></config>"
                ),
                true
            );
        } else {
            $configFilePath = $this->getModuleDir('etc', $modName) . DS . $configFile;
            if ($mergeModel->loadFile($configFilePath)) {
                $mergeToObject->extend($mergeModel, true);
            }
        }
    }

    /**
     * Go through all modules and find configuration files of active modules
     *
     * @param Magento_Core_Model_ConfigInterface $modulesConfig
     * @param $filename
     * @return array
     */
    public function getConfigurationFiles(Magento_Core_Model_ConfigInterface $modulesConfig, $filename)
    {
        $result = array();
        $modules = $modulesConfig->getNode('modules')->children();
        /** @var $module Magento_Core_Model_Config_Element */
        foreach ($modules as $moduleName => $module) {
            if ((!$module->is('active'))) {
                continue;
            }
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
        if (isset($this->_moduleDirs[$moduleName][$type])) {
            return $this->_moduleDirs[$moduleName][$type];
        }

        $dir = $this->_dirs->getDir(Magento_Core_Model_Dir::MODULES) . DIRECTORY_SEPARATOR
            . uc_words($moduleName, DIRECTORY_SEPARATOR);

        switch ($type) {
            case 'etc':
            case 'controllers':
            case 'sql':
            case 'data':
            case 'locale':
            case 'view':
                $dir .= DS . $type;
                break;
        }

        $dir = str_replace('/', DS, $dir);
        return $dir;
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
        if (!isset($this->_moduleDirs[$moduleName])) {
            $this->_moduleDirs[$moduleName] = array();
        }
        $this->_moduleDirs[$moduleName][$type] = $path;
    }
}
