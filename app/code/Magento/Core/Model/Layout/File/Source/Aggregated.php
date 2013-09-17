<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Source of layout files aggregated from a theme and its parents according to merging and overriding conventions
 */
class Magento_Core_Model_Layout_File_Source_Aggregated implements Magento_Core_Model_Layout_File_SourceInterface
{
    /**
     * @var Magento_Core_Model_Layout_File_List_Factory
     */
    private $_fileListFactory;

    /**
     * @var Magento_Core_Model_Layout_File_SourceInterface
     */
    private $_baseFiles;

    /**
     * @var Magento_Core_Model_Layout_File_SourceInterface
     */
    private $_themeFiles;

    /**
     * @var Magento_Core_Model_Layout_File_SourceInterface
     */
    private $_overridingBaseFiles;

    /**
     * @var Magento_Core_Model_Layout_File_SourceInterface
     */
    private $_overridingThemeFiles;

    /**
     * @param Magento_Core_Model_Layout_File_List_Factory $fileListFactory
     * @param Magento_Core_Model_Layout_File_SourceInterface $baseFiles
     * @param Magento_Core_Model_Layout_File_SourceInterface $themeFiles
     * @param Magento_Core_Model_Layout_File_SourceInterface $overridingBaseFiles
     * @param Magento_Core_Model_Layout_File_SourceInterface $overridingThemeFiles
     */
    public function __construct(
        Magento_Core_Model_Layout_File_List_Factory $fileListFactory,
        Magento_Core_Model_Layout_File_SourceInterface $baseFiles,
        Magento_Core_Model_Layout_File_SourceInterface $themeFiles,
        Magento_Core_Model_Layout_File_SourceInterface $overridingBaseFiles,
        Magento_Core_Model_Layout_File_SourceInterface $overridingThemeFiles
    ) {
        $this->_fileListFactory = $fileListFactory;
        $this->_baseFiles = $baseFiles;
        $this->_themeFiles = $themeFiles;
        $this->_overridingBaseFiles = $overridingBaseFiles;
        $this->_overridingThemeFiles = $overridingThemeFiles;
    }

    /**
     * Aggregate layout files from modules and a theme and its ancestors
     *
     * {@inheritdoc}
     */
    public function getFiles(Magento_Core_Model_ThemeInterface $theme)
    {
        $list = $this->_fileListFactory->create();
        $list->add($this->_baseFiles->getFiles($theme));
        foreach ($this->_getInheritedThemes($theme) as $currentTheme) {
            $list->add($this->_themeFiles->getFiles($currentTheme));
            $list->replace($this->_overridingBaseFiles->getFiles($currentTheme));
            $list->replace($this->_overridingThemeFiles->getFiles($currentTheme));
        }
        return $list->getAll();
    }

    /**
     * Return the full theme inheritance sequence, from the root theme till a specified one
     *
     * @param Magento_Core_Model_ThemeInterface $theme
     * @return Magento_Core_Model_ThemeInterface[] Format: array([<root_theme>, ..., <parent_theme>,] <current_theme>)
     */
    protected function _getInheritedThemes(Magento_Core_Model_ThemeInterface $theme)
    {
        $result = array();
        while ($theme) {
            $result[] = $theme;
            $theme = $theme->getParentTheme();
        }
        return array_reverse($result);
    }
}
