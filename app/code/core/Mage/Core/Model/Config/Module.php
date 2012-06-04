<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Configuration for modules availability and dependencies
 */
class Mage_Core_Model_Config_Module extends Mage_Core_Model_Config_Base
{
    /**
     * Types of dependencies between modules
     */
    const DEPENDENCY_TYPE_SOFT = 'soft';
    const DEPENDENCY_TYPE_HARD = 'hard';

    /**
     * @var Mage_Core_Helper_Abstract
     */
    protected $_helper;

    /**
     * Constructor
     *
     * @param Mage_Core_Model_Config_Base $modulesConfig Modules configuration merged from the config files
     * @param array $allowedModules When not empty, defines modules to be taken into account
     * @param Mage_Core_Helper_Abstract $helper
     */
    public function __construct(
        Mage_Core_Model_Config_Base $modulesConfig, $allowedModules = array(), Mage_Core_Helper_Abstract $helper = null
    ) {
        // initialize empty modules configuration
        parent::__construct('<config><modules/></config>');

        $this->_helper = $helper ?: Mage::helper('Mage_Core_Helper_Data');

        // exclude disallowed modules
        $moduleDependencies = array();
        foreach ($this->_loadModuleDependencies($modulesConfig) as $moduleName => $moduleInfo) {
            if (empty($allowedModules) || in_array($moduleName, $allowedModules)) {
                $moduleDependencies[$moduleName] = $moduleInfo;
            }
        }

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
     * Load module dependencies into an array structure
     *
     * @param Mage_Core_Model_Config_Base $modulesConfig
     * @return array
     */
    protected function _loadModuleDependencies(Mage_Core_Model_Config_Base $modulesConfig)
    {
        $result = array();
        foreach ($modulesConfig->getNode('modules')->children() as $moduleName => $moduleNode) {
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
                'active'       => 'true' === (string)$moduleNode->active,
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
            throw new UnexpectedValueException('Unsupported value of the XML attribute "type".');
        }
        return $result;
    }

    /**
     * Check whether module requirements are fulfilled
     *
     * @param array $moduleDependencies
     * @throws Mage_Core_Exception
     */
    protected function _checkModuleRequirements(array $moduleDependencies)
    {
        foreach ($moduleDependencies as $moduleName => $moduleInfo) {
            if (!$moduleInfo['active']) {
                continue;
            }
            foreach ($moduleInfo['dependencies'] as $relatedModuleName => $dependencyType) {
                $relatedModuleActive = !empty($moduleDependencies[$relatedModuleName]['active']);
                if (!$relatedModuleActive && $dependencyType == self::DEPENDENCY_TYPE_HARD) {
                    Mage::throwException($this->_helper->__(
                        'Module "%1$s" requires module "%2$s".', $moduleName, $relatedModuleName
                    ));
                }
            }
        }
    }

    /**
     * Check module dependencies and sort, so that dependent modules go after ones they depend on
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
     * @throws Mage_Core_Exception
     */
    protected function _getAllDependencies(array $moduleDependencies, $moduleName, $usedModules = array())
    {
        if (empty($moduleDependencies[$moduleName]['active'])) {
            // do not take inactive modules into account, they will not be used anyway
            return array();
        }
        $usedModules[] = $moduleName;
        $result = $moduleDependencies[$moduleName]['dependencies'];
        foreach (array_keys($result) as $relatedModuleName) {
            if (in_array($relatedModuleName, $usedModules)) {
                Mage::throwException($this->_helper->__(
                    'Module "%1$s" cannot depend on "%2$s".', $moduleName, $relatedModuleName
                ));
            }
            $relatedDependencies = $this->_getAllDependencies($moduleDependencies, $relatedModuleName, $usedModules);
            $result = array_merge($result, $relatedDependencies);
        }
        return $result;
    }
}
