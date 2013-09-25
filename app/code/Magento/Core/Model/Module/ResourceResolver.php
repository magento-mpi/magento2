<?php
/**
 * Resource resolver is used to retrieve a list of resources declared by module
 *
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
class Magento_Core_Model_Module_ResourceResolver implements Magento_Core_Model_Module_ResourceResolverInterface
{
    /**
     * @var Magento_Core_Model_Config_Modules_Reader
     */
    protected $_moduleReader;

    /**
     * Map that contains cached resources per module
     *
     * @var array
     */
    protected $_moduleResources = array();

    /**
     * @param Magento_Core_Model_Config_Modules_Reader $moduleReader
     */
    public function __construct(Magento_Core_Model_Config_Modules_Reader $moduleReader)
    {
        $this->_moduleReader = $moduleReader;
    }

    /**
     * Retrieve the list of resources declared by the given module
     *
     * @param string $moduleName
     * @return array
     */
    public function getResourceList($moduleName)
    {
        if (!isset($this->_moduleResources[$moduleName])) {
            // Process sub-directories within modules sql directory
            $moduleSqlDir = $this->_moduleReader->getModuleDir('sql', $moduleName);
            $sqlResources = array();
            foreach (glob($moduleSqlDir . DIRECTORY_SEPARATOR . '*', GLOB_ONLYDIR) as $resourceDir) {
                $sqlResources[] = basename($resourceDir);
            }
            $moduleDataDir = $this->_moduleReader->getModuleDir('data', $moduleName);
            // Process sub-directories within modules data directory
            $dataResources = array();
            foreach (glob($moduleDataDir . DIRECTORY_SEPARATOR . '*', GLOB_ONLYDIR) as $resourceDir) {
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
