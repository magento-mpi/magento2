<?php
/**
 * Dependency manager, checks if all dependencies on modules and extensions are satisfied
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Setup\Module\Dependency;

class Manager implements ManagerInterface
{
    /**
     * Check dependencies of the given module
     *
     * @param array $moduleConfig
     * @param array $activeModules
     * @return void
     * @throws \Exception
     */
    public function checkModuleDependencies(array $moduleConfig, array $activeModules = array())
    {
        // Check that required modules are active
        if ($activeModules) {
            foreach ($moduleConfig['dependencies']['modules'] as $moduleName) {
                if (!isset($activeModules[$moduleName])) {
                    throw new \Exception(
                        "Module '{$moduleConfig['name']}' depends on '{$moduleName}' that is missing or not active."
                    );
                }
            }
        }

        // Check that required extensions are loaded
        foreach ($moduleConfig['dependencies']['extensions']['strict'] as $extensionData) {
            $extensionName = $extensionData['name'];
            $minVersion = isset($extensionData['minVersion']) ? $extensionData['minVersion'] : null;
            if (!$this->isPhpExtensionLoaded($extensionName, $minVersion)) {
                throw new \Exception(
                    "Module '{$moduleConfig['name']}' depends on '{$extensionName}' PHP extension that is not loaded."
                );
            }
        }
        foreach ($moduleConfig['dependencies']['extensions']['alternatives'] as $altExtensions) {
            $this->checkAlternativeExtensions($moduleConfig['name'], $altExtensions);
        }
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
    public function getExtendedModuleDependencies($moduleName, array $modules, array $usedModules = array())
    {
        $usedModules[] = $moduleName;
        $dependencyList = $modules[$moduleName]['dependencies']['modules'];
        foreach ($dependencyList as $relModuleName) {
            if (in_array($relModuleName, $usedModules)) {
                throw new \Exception(
                    "Module '{$moduleName}' cannot depend on '{$relModuleName}' since it creates circular dependency."
                );
            }
            if (empty($modules[$relModuleName])) {
                continue;
            }
            $relDependencies = $this->getExtendedModuleDependencies($relModuleName, $modules, $usedModules);
            $dependencyList = array_unique(array_merge($dependencyList, $relDependencies));
        }
        return $dependencyList;
    }

    /**
     * Check if at least one of the extensions is loaded
     *
     * @param string $moduleName
     * @param array $altExtensions
     * @return void
     * @throws \Exception
     */
    protected function checkAlternativeExtensions($moduleName, array $altExtensions)
    {
        $extensionNames = array();
        foreach ($altExtensions as $extensionData) {
            $extensionName = $extensionData['name'];
            $minVersion = isset($extensionData['minVersion']) ? $extensionData['minVersion'] : null;
            if ($this->isPhpExtensionLoaded($extensionName, $minVersion)) {
                return;
            }
            $extensionNames[] = $extensionName;
        }
        if (!empty($extensionNames)) {
            throw new \Exception(
                "Module '{$moduleName}' depends on at least one of the following PHP extensions: " . implode(
                    ',',
                    $extensionNames
                ) . '.'
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
    protected function isPhpExtensionLoaded($extensionName, $minVersion = null)
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
}
