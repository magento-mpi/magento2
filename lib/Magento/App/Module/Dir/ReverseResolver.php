<?php
/**
 * Resolves file/directory paths to modules they belong to
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\App\Module\Dir;

class ReverseResolver
{
    /**
     * @var \Magento\Module\ModuleListInterface
     */
    protected $_moduleList;

    /**
     * @var \Magento\App\Module\Dir
     */
    protected $_moduleDirs;

    /**
     * @param \Magento\Module\ModuleListInterface $moduleList
     * @param \Magento\App\Module\Dir $moduleDirs
     */
    public function __construct(
        \Magento\Module\ModuleListInterface $moduleList,
        \Magento\App\Module\Dir $moduleDirs
    ) {
        $this->_moduleList = $moduleList;
        $this->_moduleDirs = $moduleDirs;
    }

    /**
     * Retrieve fully-qualified module name, path belongs to
     *
     * @param string $path Full path to file or directory
     * @return string|null
     */
    public function getModuleName($path)
    {
        $path = str_replace('\\', '/', $path);
        foreach (array_keys($this->_moduleList->getModules()) as $moduleName) {
            $moduleDir = $this->_moduleDirs->getDir($moduleName);
            $moduleDir = str_replace('\\', '/', $moduleDir);
            if ($path == $moduleDir || strpos($path, $moduleDir . '/') === 0) {
                return $moduleName;
            }
        }
        return null;
    }
}
