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
     * Constructor
     *
     * @param Mage_Core_Model_Config_Base $modulesConfig Modules configuration merged from the config files
     * @param array $allowedModules When not empty, defines modules to be taken into account
     * @throws UnexpectedValueException
     */
    public function __construct(Mage_Core_Model_Config_Base $modulesConfig, array $allowedModules = array())
    {
        // initialize empty modules configuration
        parent::__construct('<config><modules/></config>');

        $moduleDependencies = array();
        foreach ($modulesConfig->getNode('modules')->children() as $moduleName => $moduleNode) {
            // exclude not allowed modules
            if ($allowedModules && in_array($moduleName, $allowedModules)) {
                continue;
            }
            $dependencies = array();
            if ($moduleNode->depends) {
                /** @var $dependencyNode Varien_Simplexml_Element */
                foreach ($moduleNode->depends->children() as $dependencyNode) {
                    $dependencyModuleName = $dependencyNode->getName();
                    $dependencyType = $dependencyNode->getAttribute('type') ?: self::DEPENDENCY_TYPE_HARD;
                    if (!in_array($dependencyType, array(self::DEPENDENCY_TYPE_HARD, self::DEPENDENCY_TYPE_SOFT))) {
                        throw new UnexpectedValueException('Unsupported value of the XML attribute "type".');
                    }
                    $dependencies[$dependencyModuleName] = $dependencyType;
                }
            }
            $moduleDependencies[$moduleName] = array(
                'module'       => $moduleName,
                'active'       => 'true' === (string)$moduleNode->active,
                'dependencies' => $dependencies,
            );
        }

        // check module dependencies and sort, so that dependent modules go after ones they depend on
        $moduleDependencies = $this->_sortModuleDepends($moduleDependencies);

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
     * Check module dependencies and sort, so that dependent modules go after ones they depend on
     *
     * @param array $moduleDependencies
     * @return array
     */
    protected function _sortModuleDepends(array $moduleDependencies)
    {
        // check module requirements and extend dependencies with indirect ones
        $activeModules = array();
        foreach ($moduleDependencies as $moduleName => $moduleInfo) {
            if (!$moduleInfo['active']) {
                continue;
            }
            $dependencies = $moduleInfo['dependencies'];
            foreach ($moduleInfo['dependencies'] as $relatedModuleName => $dependencyType) {
                $relateModuleActive = !empty($moduleDependencies[$relatedModuleName]['active']);
                if ($relateModuleActive) {
                    $dependencies = array_merge($dependencies, $moduleDependencies[$relatedModuleName]['dependencies']);
                } else if ($dependencyType == self::DEPENDENCY_TYPE_HARD) {
                    Mage::throwException(Mage::helper('Mage_Core_Helper_Data')->__(
                        'Module "%1$s" requires module "%2$s".', $moduleName, $relatedModuleName
                    ));
                }
            }
            $moduleDependencies[$moduleName]['dependencies'] = $dependencies;
            $activeModules[] = $moduleName;
        }

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

        // check for circular dependencies (modules, which after sorting still depends on ones defined after them)
        $definedModules = array();
        foreach ($moduleDependencies as $moduleInfo) {
            foreach ($moduleInfo['dependencies'] as $relatedModuleName => $dependencyType) {
                if (!isset($definedModules[$relatedModuleName]) && in_array($relatedModuleName, $activeModules)) {
                    Mage::throwException(Mage::helper('Mage_Core_Helper_Data')->__(
                        'Module "%1$s" cannot depend on "%2$s".', $moduleInfo['module'], $relatedModuleName
                    ));
                }
            }
            $definedModules[$moduleInfo['module']] = true;
        }

        return $moduleDependencies;
    }
}
