<?php
/**
 * Module configuration loader
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Core_Model_Config_Loader_Modules implements Mage_Core_Model_Config_LoaderInterface
{
    /**
     * Primary application configuration
     *
     * @var Mage_Core_Model_Config_Primary
     */
    protected $_primaryConfig;

    /**
     * Load modules configuration
    /**
     * @var Mage_Core_Model_Dir
     */
    protected $_dirs;

    /**
     * Loaded modules
     *
     * @var array
     */
    protected $_modulesCache = array();

    /**
     * List of modules that should be loaded
     *
     * @var array
     */
    protected $_allowedModules = array();

    /**
     * @param Mage_Core_Model_Config_Primary $primaryConfig
     * @param Mage_Core_Model_Dir $dirs
     * @param array $allowedModules
     */
    public function __construct(
        Mage_Core_Model_Config_Primary $primaryConfig, Mage_Core_Model_Dir $dirs, array $allowedModules = array()
    ) {
        $this->_dirs = $dirs;
        $this->_primaryConfig = $primaryConfig;
        $this->_allowedModules = $allowedModules;
    }

    /**
     * Populate configuration object with modules configuration
     *
     * @param Mage_Core_Model_Config_Base $config
     */
    public function load(Mage_Core_Model_Config_Base $config)
    {
        Magento_Profiler::start('config');
        Magento_Profiler::start('load_modules');
        $this->_loadDeclaredModules($config);

        Magento_Profiler::start('load_modules_configuration');
        $resourceConfig = sprintf('config.%s.xml', $this->_primaryConfig->getResourceConnectionModel());
        $this->loadModulesConfiguration(array('config.xml', $resourceConfig), $config);
        Magento_Profiler::stop('load_modules_configuration');

        $config->applyExtends();
        Magento_Profiler::stop('load_modules');
        Magento_Profiler::stop('config');
    }

    /**
     * Load declared modules configuration
     */
    protected function _loadDeclaredModules(Mage_Core_Model_Config_Base $mergeToConfig)
    {
        Magento_Profiler::start('load_modules_files');
        $moduleFiles = $this->_getDeclaredModuleFiles();
        if (!$moduleFiles) {
            return;
        }
        Magento_Profiler::stop('load_modules_files');

        Magento_Profiler::start('load_modules_declaration');
        $unsortedConfig = new Mage_Core_Model_Config_Base('<config/>');
        $emptyConfig = new Mage_Core_Model_Config_Element('<config><modules/></config>');
        $declaredModules = array();
        foreach ($moduleFiles as $oneConfigFile) {
            $path = explode(DIRECTORY_SEPARATOR, $oneConfigFile);
            $moduleConfig = new Mage_Core_Model_Config_Base($oneConfigFile);
            $modules = $moduleConfig->getXpath('modules/*');
            if (!$modules) {
                continue;
            }
            $cPath = count($path);
            if ($cPath > 4) {
                $moduleName = $path[$cPath - 4] . '_' . $path[$cPath - 3];
                $this->_modulesCache[$moduleName] = $moduleConfig;
            }
            foreach ($modules as $module) {
                $moduleName = $module->getName();
                $isActive = (string)$module->active;
                if (isset($declaredModules[$moduleName])) {
                    $declaredModules[$moduleName]['active'] = $isActive;
                    continue;
                }
                $newModule = clone $emptyConfig;
                $newModule->modules->appendChild($module);
                $declaredModules[$moduleName] = array(
                    'active' => $isActive,
                    'module' => $newModule,
                );
            }
        }
        foreach ($declaredModules as $moduleName => $module) {
            if ($module['active'] == 'true') {
                $module['module']->modules->{$moduleName}->active = 'true';
                $unsortedConfig->extend(new Mage_Core_Model_Config_Base($module['module']));
            }
        }
        $sortedConfig = new Mage_Core_Model_Config_Modules_Sorted($unsortedConfig, $this->_allowedModules);

        $mergeToConfig->extend($sortedConfig);
        Magento_Profiler::stop('load_modules_declaration');
    }

    /**
     * Retrieve Declared Module file list
     *
     * @return array
     */
    protected function _getDeclaredModuleFiles()
    {
        $codeDir = $this->_dirs->getDir(Mage_Core_Model_Dir::MODULES);
        $moduleFiles = glob($codeDir . DS . '*' . DS . '*' . DS . '*' . DS . 'etc' . DS . 'config.xml');

        if (!$moduleFiles) {
            return false;
        }

        $collectModuleFiles = array(
            'base'   => array(),
            'mage'   => array(),
            'custom' => array()
        );

        foreach ($moduleFiles as $v) {
            $name = explode(DIRECTORY_SEPARATOR, $v);
            $collection = $name[count($name) - 4];

            if ($collection == 'Mage') {
                $collectModuleFiles['mage'][] = $v;
            } else {
                $collectModuleFiles['custom'][] = $v;
            }
        }

        $etcDir = $this->_dirs->getDir(Mage_Core_Model_Dir::CONFIG);
        $additionalFiles = glob($etcDir . DS . 'modules' . DS . '*.xml');

        foreach ($additionalFiles as $v) {
            $collectModuleFiles['base'][] = $v;
        }

        return array_merge(
            $collectModuleFiles['mage'],
            $collectModuleFiles['custom'],
            $collectModuleFiles['base']
        );
    }

    /**
     * Iterate all active modules "etc" folders and combine data from
     * specidied xml file name to one object
     *
     * @param   string $fileName
     * @param   null|Mage_Core_Model_Config_Base $mergeToObject
     * @return  Mage_Core_Model_Config_Base
     */
    public function loadModulesConfiguration($fileName, $mergeToObject = null, $mergeModel=null)
    {
        if ($mergeToObject === null) {
            $mergeToObject = clone $this->_prototypeFactory->create('<config/>');
        }
        if ($mergeModel === null) {
            $mergeModel = clone $this->_prototypeFactory->create('<config/>');
        }
        $modules = $this->getNode('modules')->children();
        foreach ($modules as $modName=>$module) {
            if ($module->is('active')) {
                if (!is_array($fileName)) {
                    $fileName = array($fileName);
                }
                foreach ($fileName as $configFile) {
                    if ($configFile == 'config.xml' && isset($this->_modulesCache[$modName])) {
                        $mergeToObject->extend($this->_modulesCache[$modName], true);
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
            }
        }
        unset($this->_modulesCache);
        return $mergeToObject;
    }

    /**
     * Go through all modules and find configuration files of active modules
     *
     * @param string $filename
     * @return array
     */
    public function getModuleConfigurationFiles($filename)
    {
        $result = array();
        $modules = $this->getNode('modules')->children();
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
}
