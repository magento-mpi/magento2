<?php
/**
 * Module declaration reader. Reads module.xml declaration files from module /etc directories.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Module\Declaration\Reader;

use Magento\Module\Declaration\FileResolver;
use Magento\Module\Declaration\Converter\Dom;
use Magento\Module\Declaration\SchemaLocator;
use Magento\Config\ValidationStateInterface;

class Filesystem extends \Magento\Config\Reader\Filesystem
{
    /**
     * The list of allowed modules
     *
     * @var array
     */
    protected $_allowedModules;

    /**
     * {@inheritdoc}
     */
    protected $_idAttributes = array(
        '/config/module' => 'name',
        '/config/module/depends/extension' => 'name',
        '/config/module/depends/choice/extension' => 'name',
        '/config/module/sequence/module' => 'name',
    );

    /**
     * @param FileResolver $fileResolver
     * @param Dom $converter
     * @param SchemaLocator $schemaLocator
     * @param ValidationStateInterface $validationState
     * @param string $fileName
     * @param array $idAttributes
     * @param string $domDocumentClass
     * @param string $defaultScope
     * @param array $allowedModules
     */
    public function __construct(
        FileResolver $fileResolver,
        Dom $converter,
        SchemaLocator $schemaLocator,
        ValidationStateInterface $validationState,
        $fileName = 'module.xml',
        $idAttributes = array(),
        $domDocumentClass = 'Magento\Config\Dom',
        $defaultScope = 'global',
        array $allowedModules = array()
    ) {
        parent::__construct(
            $fileResolver,
            $converter,
            $schemaLocator,
            $validationState,
            $fileName,
            $idAttributes,
            $domDocumentClass,
            $defaultScope
        );
        $this->_allowedModules = $allowedModules;
    }

    /**
     * {@inheritdoc}
     */
    public function read($scope = null)
    {
        $activeModules = $this->_filterActiveModules(parent::read($scope));
        foreach ($activeModules as $moduleConfig) {
            $this->_checkModuleDependencies($moduleConfig, $activeModules);
        }
        return $this->_sortModules($activeModules);
    }

    /**
     * Retrieve declarations of active modules
     *
     * @param array $modules
     * @return array
     */
    protected function _filterActiveModules(array $modules)
    {
        $activeModules = array();
        foreach ($modules as $moduleName => $moduleConfig) {
            if ($moduleConfig['active']
                && (empty($this->_allowedModules) || in_array($moduleConfig['name'], $this->_allowedModules))
            ) {
                $activeModules[$moduleName] = $moduleConfig;
            }
        }
        return $activeModules;
    }

    /**
     * Check dependencies of the given module
     *
     * @param array $moduleConfig
     * @param array $activeModules
     * @return void
     * @throws \Exception
     */
    protected function _checkModuleDependencies(array $moduleConfig, array $activeModules)
    {
        // Check that required modules are active
        foreach ($moduleConfig['dependencies']['modules'] as $moduleName) {
            if (!isset($activeModules[$moduleName])) {
                throw new \Exception(
                    "Module '{$moduleConfig['name']}' depends on '{$moduleName}' that is missing or not active."
                );
            }
        }
        // Check that required extensions are loaded
        foreach ($moduleConfig['dependencies']['extensions']['strict'] as $extensionData) {
            $extensionName = $extensionData['name'];
            $minVersion = isset($extensionData['minVersion']) ? $extensionData['minVersion'] : null;
            if (!$this->_isPhpExtensionLoaded($extensionName, $minVersion)) {
                throw new \Exception(
                    "Module '{$moduleConfig['name']}' depends on '{$extensionName}' PHP extension that is not loaded."
                );
            }
        }
        foreach ($moduleConfig['dependencies']['extensions']['alternatives'] as $altExtensions) {
            $this->_checkAlternativeExtensions($moduleConfig['name'], $altExtensions);
        }
    }

    /**
     * Check if at least one of the extensions is loaded
     *
     * @param string $moduleName
     * @param array $altExtensions
     * @return void
     * @throws \Exception
     */
    protected function _checkAlternativeExtensions($moduleName, array $altExtensions)
    {
        $extensionNames = array();
        foreach ($altExtensions as $extensionData) {
            $extensionName = $extensionData['name'];
            $minVersion = isset($extensionData['minVersion']) ? $extensionData['minVersion'] : null;
            if ($this->_isPhpExtensionLoaded($extensionName, $minVersion)) {
                return;
            }
            $extensionNames[] = $extensionName;
        }
        if (!empty($extensionNames)) {
            throw new \Exception(
                "Module '{$moduleName}' depends on at least one of the following PHP extensions: "
                    . implode(',', $extensionNames) . '.'
            );
        }
        return;
    }

    /**
     * Check if required version of PHP extension is loaded
     *
     * @param string $extensionName
     * @param string|null $minVersion
     * @return boolean
     */
    protected function _isPhpExtensionLoaded($extensionName, $minVersion = null)
    {
        if (extension_loaded($extensionName)) {
            if (is_null($minVersion)) {
                return true;
            } elseif (version_compare($minVersion, phpversion($extensionName), '<=')) {
                return true;
            }
        }
        return false;
    }

    /**
     * Sort module declarations based on module dependencies
     *
     * @param array $modules
     * @return array
     * @SuppressWarnings(PHPMD.ShortVariable)
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    protected function _sortModules(array $modules)
    {
        /**
         * The following map is needed only for sorting
         * (in order not to add extra information about dependencies to module config)
         */
        $moduleDependencyMap = array();
        foreach ($modules as $moduleName => $value) {
            $moduleDependencyMap[] = array(
                'moduleName' => $moduleName,
                'dependencies' => $this->_getExtendedModuleDependencies($moduleName, $modules),
            );
        }

        // Use "bubble sorting" because usort does not check each pair of elements and in this case it is important
        $modulesCount = count($moduleDependencyMap);
        for ($i = 0; $i < $modulesCount - 1; $i++) {
            for ($j = $i; $j < $modulesCount; $j++) {
                if (in_array($moduleDependencyMap[$j]['moduleName'], $moduleDependencyMap[$i]['dependencies'])) {
                    $temp = $moduleDependencyMap[$i];
                    $moduleDependencyMap[$i] = $moduleDependencyMap[$j];
                    $moduleDependencyMap[$j] = $temp;
                }
            }
        }

        $sortedModules = array();
        foreach ($moduleDependencyMap as $moduleDependencyPair) {
            $sortedModules[$moduleDependencyPair['moduleName']] = $modules[$moduleDependencyPair['moduleName']];
        }

        return $sortedModules;
    }

    /**
     * Recursively identify all module dependencies and detect circular ones
     *
     * @param string $moduleName
     * @param array $modules
     * @param array $usedModules
     * @return array
     * @throws \Exception
     */
    protected function _getExtendedModuleDependencies($moduleName,  array $modules, array $usedModules = array())
    {
        $usedModules[] = $moduleName;
        $dependencyList = $modules[$moduleName]['dependencies']['modules'];
        foreach ($dependencyList as $relatedModuleName) {
            if (in_array($relatedModuleName, $usedModules)) {
                throw new \Exception(
                    "Module '$moduleName' cannot depend on '$relatedModuleName' since it creates circular dependency."
                );
            }
            if (empty($modules[$relatedModuleName])) {
                continue;
            }
            $relatedDependencies = $this->_getExtendedModuleDependencies($relatedModuleName, $modules, $usedModules);
            $dependencyList = array_unique(array_merge($dependencyList, $relatedDependencies));
        }
        return $dependencyList;
    }
}
