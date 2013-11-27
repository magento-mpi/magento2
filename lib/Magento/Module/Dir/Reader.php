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

use Magento\Filesystem\Directory\Read;
use Magento\Filesystem;

class Reader
{
    /**
     * Module directories that were set explicitly
     *
     * @var array
     */
    protected $customModuleDirs = array();

    /**
     * Directory registry
     *
     * @var \Magento\Module\Dir
     */
    protected $moduleDirs;

    /**
     * Modules configuration provider
     *
     * @var \Magento\Module\ModuleListInterface
     */
    protected $modulesList;

    /**
     * @var Read
     */
    protected $modulesDirectory;

    /**
     * @param \Magento\Module\Dir $moduleDirs
     * @param \Magento\Module\ModuleListInterface $moduleList
     * @param \Magento\Filesystem $filesystem
     */
    public function __construct(
        \Magento\Module\Dir $moduleDirs,
        \Magento\Module\ModuleListInterface $moduleList,
        \Magento\Filesystem $filesystem
    ) {
        $this->moduleDirs = $moduleDirs;
        $this->modulesList = $moduleList;
        $this->modulesDirectory = $filesystem->getDirectoryRead(Filesystem::MODULES);
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
        foreach (array_keys($this->modulesList->getModules()) as $moduleName) {
            $file = $this->getModuleDir('etc', $moduleName) . '/' . $filename;
            if ($this->modulesDirectory->isExist($file)) {
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
        if (isset($this->customModuleDirs[$moduleName][$type])) {
            return $this->customModuleDirs[$moduleName][$type];
        }
        return $this->moduleDirs->getDir($moduleName, $type);
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
        $this->customModuleDirs[$moduleName][$type] = $path;
    }
}
