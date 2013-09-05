<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Source of base layout files introduced by modules
 */
class Magento_Core_Model_Layout_File_Source_Base implements Magento_Core_Model_Layout_File_SourceInterface
{
    /**
     * @var \Magento\Filesystem
     */
    private $_filesystem;

    /**
     * @var Magento_Core_Model_Dir
     */
    private $_dirs;

    /**
     * @var Magento_Core_Model_Layout_File_Factory
     */
    private $_fileFactory;

    /**
     * @param \Magento\Filesystem $filesystem
     * @param Magento_Core_Model_Dir $dirs
     * @param Magento_Core_Model_Layout_File_Factory $fileFactory
     */
    public function __construct(
        \Magento\Filesystem $filesystem,
        Magento_Core_Model_Dir $dirs,
        Magento_Core_Model_Layout_File_Factory $fileFactory
    ) {
        $this->_filesystem = $filesystem;
        $this->_dirs = $dirs;
        $this->_fileFactory = $fileFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function getFiles(Magento_Core_Model_ThemeInterface $theme)
    {
        $namespace = $module = '*';
        $area = $theme->getArea();
        $files = $this->_filesystem->searchKeys(
            $this->_dirs->getDir(Magento_Core_Model_Dir::MODULES),
            "{$namespace}/{$module}/view/{$area}/layout/*.xml"
        );
        $result = array();
        foreach ($files as $filename) {
            $moduleDir = dirname(dirname(dirname(dirname($filename))));
            $module = basename($moduleDir);
            $namespace = basename(dirname($moduleDir));
            $moduleFull = "{$namespace}_{$module}";
            $result[] = $this->_fileFactory->create($filename, $moduleFull);
        }
        return $result;
    }
}
