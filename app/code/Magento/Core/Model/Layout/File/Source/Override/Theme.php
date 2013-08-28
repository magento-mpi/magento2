<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Source of layout files that explicitly override files of ancestor themes
 */
class Magento_Core_Model_Layout_File_Source_Override_Theme implements Magento_Core_Model_Layout_File_SourceInterface
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
            "{$themePath}/{$namespace}_{$module}/layout/override/*/*.xml"
        );

        if (empty($files)) {
            return array();
        }

        $themes = array();
        $currentTheme = $theme;
        while ($currentTheme = $currentTheme->getParentTheme()) {
            $themes[$currentTheme->getCode()] = $currentTheme;
        }

        $result = array();
        foreach ($files as $filename) {
            if (preg_match("#([^/]+)/layout/override/([^/]+)/[^/]+\.xml$#i", $filename, $matches)) {
                $moduleFull = $matches[1];
                $ancestorThemeCode = $matches[2];
                if (!isset($themes[$ancestorThemeCode])) {
                    throw new Magento_Core_Exception(sprintf(
                        "Trying to override layout file '%s' for theme '%s', which is not ancestor of theme '%s'",
                        $filename, $ancestorThemeCode, $theme->getCode()
                    ));
                }
                $result[] = $this->_fileFactory->create($filename, $moduleFull, $themes[$ancestorThemeCode]);
            }
        }
        return $result;
    }
}
