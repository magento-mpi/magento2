<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Setup\Module;

interface ModuleListInterface
{
    /**
     * Get configuration of all declared active modules
     *
     * @return array
     */
    public function getModules();

    /**
     * Get module configuration
     *
     * @param string $moduleName
     * @return array
     */
    public function getModule($moduleName);
}
