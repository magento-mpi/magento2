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
class Mage_Core_Model_Layout_File_Source_Aggregated implements Mage_Core_Model_Layout_File_SourceInterface
{
    /**
     * @var Mage_Core_Model_Layout_File_List_Factory
     */
    private $_fileListFactory;

    /**
     * @var Mage_Core_Model_Layout_File_SourceInterface
     */
    private $_baseFiles;

    /**
     * @var Mage_Core_Model_Layout_File_SourceInterface
     */
    private $_themeFiles;

    /**
     * @var Mage_Core_Model_Layout_File_SourceInterface
     */
    private $_overridingBaseFiles;

    /**
     * @var Mage_Core_Model_Layout_File_SourceInterface
     */
    private $_overridingThemeFiles;

    /**
     * @param Mage_Core_Model_Layout_File_List_Factory $fileListFactory
     * @param Mage_Core_Model_Layout_File_SourceInterface $baseFiles
     * @param Mage_Core_Model_Layout_File_SourceInterface $themeFiles
     * @param Mage_Core_Model_Layout_File_SourceInterface $overridingBaseFiles
     * @param Mage_Core_Model_Layout_File_SourceInterface $overridingThemeFiles
     */
    public function __construct(
        Mage_Core_Model_Layout_File_List_Factory $fileListFactory,
        Mage_Core_Model_Layout_File_SourceInterface $baseFiles,
        Mage_Core_Model_Layout_File_SourceInterface $themeFiles,
        Mage_Core_Model_Layout_File_SourceInterface $overridingBaseFiles,
        Mage_Core_Model_Layout_File_SourceInterface $overridingThemeFiles
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
    public function getFiles(Mage_Core_Model_ThemeInterface $theme)
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
     * @param Mage_Core_Model_ThemeInterface $theme
     * @return Mage_Core_Model_ThemeInterface[] Format: array([<root_theme>, ..., <parent_theme>,] <current_theme>)
     */
    protected function _getInheritedThemes(Mage_Core_Model_ThemeInterface $theme)
    {
        $result = array();
        while ($theme) {
            $result[] = $theme;
            $theme = $theme->getParentTheme();
        }
        return array_reverse($result);
    }
}
