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

interface ManagerInterface
{
    /**
     * Check dependencies of the given module
     *
     * @param array $moduleConfig
     * @param array $activeModules
     * @return void
     * @throws \Exception
     */
    public function checkModuleDependencies(array $moduleConfig, array $activeModules = array());

    /**
     * Recursively identify all module dependencies and detect circular ones
     *
     * @param string $moduleName
     * @param array $modules
     * @param array $usedModules
     * @return array
     * @throws \Exception
     */
    public function getExtendedModuleDependencies($moduleName, array $modules, array $usedModules = array());
}
