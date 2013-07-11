<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Source of layout files that explicitly override base files introduced by modules
 */
class Mage_Core_Model_Layout_File_Source_Override_Base implements Mage_Core_Model_Layout_File_SourceInterface
{
    /**
     * @var Magento_Filesystem
     */
    private $_filesystem;

    /**
     * @var Mage_Core_Model_Dir
     */
    private $_dirs;

    /**
     * @var Mage_Core_Model_Layout_File_Factory
     */
    private $_fileFactory;

    /**
     * @param Magento_Filesystem $filesystem
     * @param Mage_Core_Model_Dir $dirs
     * @param Mage_Core_Model_Layout_File_Factory $fileFactory
     */
    public function __construct(
        Magento_Filesystem $filesystem,
        Mage_Core_Model_Dir $dirs,
        Mage_Core_Model_Layout_File_Factory $fileFactory
    ) {
        $this->_filesystem = $filesystem;
        $this->_dirs = $dirs;
        $this->_fileFactory = $fileFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function getFiles(Mage_Core_Model_ThemeInterface $theme)
    {
        $namespace = $module = '*';
        $area = $theme->getArea();
        $themePath = $theme->getThemePath();
        $files = $this->_filesystem->searchKeys(
            $this->_dirs->getDir(Mage_Core_Model_Dir::THEMES),
            "{$area}/{$themePath}/{$namespace}_{$module}/layout/override/*.xml"
        );
        $result = array();
        foreach ($files as $filename) {
            $moduleDir = dirname(dirname(dirname($filename)));
            $moduleFull = basename($moduleDir);
            $result[] = $this->_fileFactory->create($filename, $moduleFull);
        }
        return $result;
    }
}
