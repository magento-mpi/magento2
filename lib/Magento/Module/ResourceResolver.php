<?php
/**
 * Resource resolver is used to retrieve a list of resources declared by module
 *
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Module;

use Magento\Module\Dir\Reader;

class ResourceResolver implements \Magento\Module\ResourceResolverInterface
{
    /**
     * @var Reader
     */
    protected $_moduleReader;

    /**
     * Map that contains cached resources per module
     *
     * @var array
     */
    protected $_moduleResources = array();

    /**
     * @param Reader $moduleReader
     */
    public function __construct(Dir\Reader $moduleReader)
    {
        $this->_moduleReader = $moduleReader;
    }

    /**
     * Retrieve the list of resources declared by the given module
     *
     * @param string $moduleName
     * @return string[]
     */
    public function getResourceList($moduleName)
    {
        if (!isset($this->_moduleResources[$moduleName])) {
            // Process sub-directories within modules sql directory
            $moduleSqlDir = $this->_moduleReader->getModuleDir('sql', $moduleName);
            $sqlResources = array();
            foreach (glob($moduleSqlDir . '/*', GLOB_ONLYDIR) as $resourceDir) {
                $sqlResources[] = basename($resourceDir);
            }
            $moduleDataDir = $this->_moduleReader->getModuleDir('data', $moduleName);
            // Process sub-directories within modules data directory
            $dataResources = array();
            foreach (glob($moduleDataDir . '/*', GLOB_ONLYDIR) as $resourceDir) {
                $dataResources[] = basename($resourceDir);
            }
            $this->_moduleResources[$moduleName] = array_unique(array_merge(
                $sqlResources,
                $dataResources
            ));
        }
        return $this->_moduleResources[$moduleName];
    }
}
