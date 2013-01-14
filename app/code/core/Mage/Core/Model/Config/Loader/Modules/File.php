<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Core_Model_Config_Loader_Modules_File
{
    /**
     * Modules configuration
     *
     * @var Mage_Core_Model_Config_Modules
     */
    protected $_modulesConfig;

    /**
     * Base config factory
     *
     * @var Mage_Core_Model_Config_BaseFactory
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
     * @var Mage_Core_Model_Dir
     */
    protected $_dirs;

    /**
     * @param Mage_Core_Model_Dir $dirs
     * @param Mage_Core_Model_Config_Modules $modulesConfig
     * @param Mage_Core_Model_Config_BaseFactory $prototypeFactory
     */
    public function __construct(
        Mage_Core_Model_Dir $dirs,
        Mage_Core_Model_Config_Modules $modulesConfig,
        Mage_Core_Model_Config_BaseFactory $prototypeFactory
    ) {
        $this->_dirs = $dirs;
        $this->_config = $modulesConfig;
        $this->_prototypeFactory = $prototypeFactory;
    }

    /**
     * Iterate all active modules "etc" folders and combine data from
     * specidied xml file name to one object
     *
     * @param Mage_Core_Model_Config_Base $modulesConfig
     * @param string $fileName
     * @param Mage_Core_Model_Config_Base|null $mergeToObject
     * @param Mage_Core_Model_Config_Base|null $mergeModel
     * @param array $configCache
     * @return Mage_Core_Model_Config_Base|null
     */
    public function loadConfigurationFromFile(
        Mage_Core_Model_Config_Base $modulesConfig,
        $fileName,
        $mergeToObject = null,
        $mergeModel = null,
        $configCache = array()
    ) {
        if ($mergeToObject === null) {
            $mergeToObject = clone $this->_prototypeFactory->create('<config/>');
        }
        if ($mergeModel === null) {
            $mergeModel = clone $this->_prototypeFactory->create('<config/>');
        }
        $modules = $modulesConfig->getNode('modules')->children();
        /** @var $module Varien_Simplexml_Element */
        foreach ($modules as $modName => $module) {
            if ($module->is('active')) {
                if (!is_array($fileName)) {
                    $fileName = array($fileName);
                }
                foreach ($fileName as $configFile) {
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
                        $configFilePath = $this->getModuleDir($modulesConfig, 'etc', $modName) . DS . $configFile;
                        if ($mergeModel->loadFile($configFilePath)) {
                            $mergeToObject->extend($mergeModel, true);
                        }
                    }
                }
            }
        }
        return $mergeToObject;
    }

    /**
     * Go through all modules and find configuration files of active modules
     *
     * @param Mage_Core_Model_Config_Base $modulesConfig
     * @param $filename
     * @return array
     */
    public function getConfigurationFiles(Mage_Core_Model_Config_Base $modulesConfig, $filename)
    {
        $result = array();
        $modules = $modulesConfig->getNode('modules')->children();
        foreach ($modules as $moduleName => $module) {
            if ((!$module->is('active'))) {
                continue;
            }
            $file = $this->getModuleDir($modulesConfig, 'etc', $moduleName) . DIRECTORY_SEPARATOR . $filename;
            if (file_exists($file)) {
                $result[] = $file;
            }
        }
        return $result;
    }

    /**
     * Get module directory by directory type
     *
     * @param Mage_Core_Model_Config_Base $modulesConfig
     * @param string $type
     * @param string $moduleName
     * @return string
     */
    public function getModuleDir(Mage_Core_Model_Config_Base $modulesConfig, $type, $moduleName)
    {
        if (isset($this->_moduleDirs[$moduleName][$type])) {
            return $this->_moduleDirs[$moduleName][$type];
        }

        $codePool = (string)$modulesConfig->getNode('modules/' . $moduleName . 'codePool');

        $dir = $this->_dirs->getDir(Mage_Core_Model_Dir::MODULES) . DIRECTORY_SEPARATOR
            . $codePool . DIRECTORY_SEPARATOR
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
}