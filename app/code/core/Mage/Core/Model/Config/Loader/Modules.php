<?php
/**
 * Locale configuration loader
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Core_Model_Config_Loader_Modules implements Mage_Core_Model_Config_LoaderInterface
{
    protected $_config;

    public function __construct(Mage_Core_Model_Config_Local $config)
    {
        $this->_config = $config;
    }

    /**
     * Populate configuration object
     *
     * @param Mage_Core_Model_Config_Base $config
     */
    public function load(Mage_Core_Model_Config_Base $config) //$config is empty
    {
        //load $data
        $config->extend($data);

        $config->extend($this->_config);
    }

    /**
     * Load modules configuration
     *
     * @return Mage_Core_Model_Config
     */
    protected function _loadModules()
    {
        Magento_Profiler::start('config');
        Magento_Profiler::start('load_modules');
        $this->_loadDeclaredModules();

        Magento_Profiler::start('load_modules_configuration');
        $resourceConfig = sprintf('config.%s.xml', $this->getResourceConnectionModel('core'));
        $this->loadModulesConfiguration(array('config.xml',$resourceConfig), $this);
        Magento_Profiler::stop('load_modules_configuration');

        // Prevent local configuration overriding
        $this->_loadLocalConfig();

        $this->_container->applyExtends();
        Magento_Profiler::stop('load_modules');
        Magento_Profiler::stop('config');
        return $this;
    }

    /**
     * Load declared modules configuration
     *
     * @return  Mage_Core_Model_Config
     */
    protected function _loadDeclaredModules()
    {
        Magento_Profiler::start('load_modules_files');
        $moduleFiles = $this->_getDeclaredModuleFiles();
        if (!$moduleFiles) {
            return $this;
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
        $sortedConfig = new Mage_Core_Model_Config_Module($unsortedConfig, $this->_allowedModules);

        $this->extend($sortedConfig);
        Magento_Profiler::stop('load_modules_declaration');
        return $this;
    }

    /**
     * Retrive Declared Module file list
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
     * Types of dependencies between modules
     */
    const DEPENDENCY_TYPE_SOFT = 'soft';
    const DEPENDENCY_TYPE_HARD = 'hard';

    /**
     * Constructor
     *
     * @param Mage_Core_Model_Config_Base $modulesConfig Modules configuration merged from the config files
     * @param array $allowedModules When not empty, defines modules to be taken into account
     */
    public function __construct(Mage_Core_Model_Config_Base $modulesConfig, array $allowedModules = array())
    {
        // initialize empty modules configuration
        parent::__construct('<config><modules/></config>');

        $moduleDependencies = $this->_loadModuleDependencies($modulesConfig, $allowedModules);

        $this->_checkModuleRequirements($moduleDependencies);

        $moduleDependencies = $this->_sortModuleDependencies($moduleDependencies);

        // create sorted configuration
        foreach ($modulesConfig->getNode()->children() as $nodeName => $node) {
            if ($nodeName != 'modules') {
                $this->getNode()->appendChild($node);
            }
        }
        foreach ($moduleDependencies as $moduleInfo) {
            $node = $modulesConfig->getNode('modules/' . $moduleInfo['module']);
            $this->getNode('modules')->appendChild($node);
        }
    }

    /**
     * Load dependencies for active & allowed modules into an array structure
     *
     * @param Mage_Core_Model_Config_Base $modulesConfig
     * @param array $allowedModules
     * @return array
     */
    protected function _loadModuleDependencies(Mage_Core_Model_Config_Base $modulesConfig, array $allowedModules)
    {
        $result = array();
        foreach ($modulesConfig->getNode('modules')->children() as $moduleName => $moduleNode) {
            $isModuleActive = 'true' === (string)$moduleNode->active;
            $isModuleAllowed = empty($allowedModules) || in_array($moduleName, $allowedModules);
            if (!$isModuleActive || !$isModuleAllowed) {
                continue;
            }
            $dependencies = array();
            if ($moduleNode->depends) {
                /** @var $dependencyNode Varien_Simplexml_Element */
                foreach ($moduleNode->depends->children() as $dependencyNode) {
                    $dependencyModuleName = $dependencyNode->getName();
                    $dependencies[$dependencyModuleName] = $this->_getDependencyType($dependencyNode);
                }
            }
            $result[$moduleName] = array(
                'module'       => $moduleName,
                'dependencies' => $dependencies,
            );
        }
        return $result;
    }

    /**
     * Determine dependency type from XML node that defines module dependency
     *
     * @param Varien_Simplexml_Element $dependencyNode
     * @return string
     * @throws UnexpectedValueException
     */
    protected function _getDependencyType(Varien_Simplexml_Element $dependencyNode)
    {
        $result = $dependencyNode->getAttribute('type') ?: self::DEPENDENCY_TYPE_HARD;
        if (!in_array($result, array(self::DEPENDENCY_TYPE_HARD, self::DEPENDENCY_TYPE_SOFT))) {
            $dependencyNodeXml = trim($dependencyNode->asNiceXml());
            throw new UnexpectedValueException(
                "Unknown module dependency type '$result' in declaration '$dependencyNodeXml'."
            );
        }
        return $result;
    }

    /**
     * Check whether module requirements are fulfilled
     *
     * @param array $moduleDependencies
     * @throws Magento_Exception
     */
    protected function _checkModuleRequirements(array $moduleDependencies)
    {
        foreach ($moduleDependencies as $moduleName => $moduleInfo) {
            foreach ($moduleInfo['dependencies'] as $relatedModuleName => $dependencyType) {
                $relatedModuleActive = isset($moduleDependencies[$relatedModuleName]);
                if (!$relatedModuleActive && $dependencyType == self::DEPENDENCY_TYPE_HARD) {
                    throw new Magento_Exception("Module '$moduleName' requires module '$relatedModuleName'.");
                }
            }
        }
    }

    /**
     * Sort modules until dependent modules go after ones they depend on
     *
     * @param array $moduleDependencies
     * @return array
     */
    protected function _sortModuleDependencies(array $moduleDependencies)
    {
        // add indirect dependencies
        foreach ($moduleDependencies as $moduleName => &$moduleInfo) {
            $moduleInfo['dependencies'] = $this->_getAllDependencies($moduleDependencies, $moduleName);
        }
        unset($moduleInfo);

        // "bubble sort" modules until dependent modules go after ones they depend on
        $moduleDependencies = array_values($moduleDependencies);
        $size = count($moduleDependencies) - 1;
        for ($i = $size; $i >= 0; $i--) {
            for ($j = $size; $i < $j; $j--) {
                if (isset($moduleDependencies[$i]['dependencies'][$moduleDependencies[$j]['module']])) {
                    $tempValue              = $moduleDependencies[$i];
                    $moduleDependencies[$i] = $moduleDependencies[$j];
                    $moduleDependencies[$j] = $tempValue;
                }
            }
        }

        return $moduleDependencies;
    }

    /**
     * Recursively compute all dependencies and detect circular ones
     *
     * @param array $moduleDependencies
     * @param string $moduleName
     * @param array $usedModules Keep track of used modules to detect circular dependencies
     * @return array
     * @throws Magento_Exception
     */
    protected function _getAllDependencies(array $moduleDependencies, $moduleName, $usedModules = array())
    {
        $usedModules[] = $moduleName;
        $result = $moduleDependencies[$moduleName]['dependencies'];
        foreach (array_keys($result) as $relatedModuleName) {
            if (in_array($relatedModuleName, $usedModules)) {
                throw new Magento_Exception(
                    "Module '$moduleName' cannot depend on '$relatedModuleName' since it creates circular dependency."
                );
            }
            if (empty($moduleDependencies[$relatedModuleName])) {
                continue;
            }
            $relatedDependencies = $this->_getAllDependencies($moduleDependencies, $relatedModuleName, $usedModules);
            $result = array_merge($result, $relatedDependencies);
        }
        return $result;
    }

}
