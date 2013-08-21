<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Source of non-overriding layout files introduced by a theme
 */
class Magento_Core_Model_Layout_File_Source_Theme implements Magento_Core_Model_Layout_File_SourceInterface
{
    /**
     * @var Magento_Filesystem
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
     * @param Magento_Filesystem $filesystem
     * @param Magento_Core_Model_Dir $dirs
     * @param Magento_Core_Model_Layout_File_Factory $fileFactory
     */
    public function __construct(
        Magento_Filesystem $filesystem,
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
        $themePath = $theme->getFullPath();
        $files = $this->_filesystem->searchKeys(
            $this->_dirs->getDir(Magento_Core_Model_Dir::THEMES),
            "{$themePath}/{$namespace}_{$module}/layout/*.xml"
        );
        $result = array();
        foreach ($files as $filename) {
            $moduleDir = dirname(dirname($filename));
            $moduleFull = basename($moduleDir);
            $result[] = $this->_fileFactory->create($filename, $moduleFull, $theme);
        }
        return $result;
    }
}
