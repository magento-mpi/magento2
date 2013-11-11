<?php
/**
 * Module configuration file reader
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Module\Dir;

class Reader
{
    /**
     * Module directories that were set explicitly
     *
     * @var array
     */
    protected $_customModuleDirs = array();

    /**
     * Directory registry
     *
     * @var \Magento\Module\Dir
     */
    protected $_moduleDirs;

    /**
     * Modules configuration provider
     *
     * @var \Magento\Module\ModuleListInterface
     */
    protected $_modulesList;

    /**
     * @param \Magento\Module\Dir $moduleDirs
     * @param \Magento\Module\ModuleListInterface $moduleList
     */
    public function __construct(
        \Magento\Module\Dir $moduleDirs,
        \Magento\Module\ModuleListInterface $moduleList
    ) {
        $this->_moduleDirs = $moduleDirs;
        $this->_modulesList = $moduleList;
    }

    /**
     * Go through all modules and find configuration files of active modules
     *
     * @param $filename
     * @return array
     */
    public function getConfigurationFiles($filename)
    {
        $result = array();
        foreach (array_keys($this->_modulesList->getModules()) as $moduleName) {
            $file = $this->getModuleDir('etc', $moduleName) . DIRECTORY_SEPARATOR . $filename;
            if (file_exists($file)) {
                $result[] = $file;
            }
        }
        return $result;
    }

    /**
     * Get module directory by directory type
     *
     * @param string $type
     * @param string $moduleName
     * @return string
     */
    public function getModuleDir($type, $moduleName)
    {
        if (isset($this->_customModuleDirs[$moduleName][$type])) {
            return $this->_customModuleDirs[$moduleName][$type];
        }
        return $this->_moduleDirs->getDir($moduleName, $type);
    }

    /**
     * Set path to the corresponding module directory
     *
     * @param string $moduleName
     * @param string $type directory type (etc, controllers, locale etc)
     * @param string $path
     */
    public function setModuleDir($moduleName, $type, $path)
    {
        $this->_customModuleDirs[$moduleName][$type] = $path;
    }
}
