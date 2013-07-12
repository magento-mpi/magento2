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
     * @var Mage_Core_Model_Layout_File_List
     */
    private $_fileList;

    /**
     * Whether files have been aggregated or not
     *
     * @var bool
     */
    private $_isAggregated = false;

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
     * @param Mage_Core_Model_Layout_File_List $fileList
     * @param Mage_Core_Model_Layout_File_SourceInterface $baseFiles
     * @param Mage_Core_Model_Layout_File_SourceInterface $themeFiles
     * @param Mage_Core_Model_Layout_File_SourceInterface $overridingBaseFiles
     * @param Mage_Core_Model_Layout_File_SourceInterface $overridingThemeFiles
     */
    public function __construct(
        Mage_Core_Model_Layout_File_List $fileList,
        Mage_Core_Model_Layout_File_SourceInterface $baseFiles,
        Mage_Core_Model_Layout_File_SourceInterface $themeFiles,
        Mage_Core_Model_Layout_File_SourceInterface $overridingBaseFiles,
        Mage_Core_Model_Layout_File_SourceInterface $overridingThemeFiles
    ) {
        $this->_fileList = $fileList;
        $this->_baseFiles = $baseFiles;
        $this->_themeFiles = $themeFiles;
        $this->_overridingBaseFiles = $overridingBaseFiles;
        $this->_overridingThemeFiles = $overridingThemeFiles;
    }

    /**
     * {@inheritdoc}
     */
    public function getFiles(Mage_Core_Model_ThemeInterface $theme)
    {
        if (!$this->_isAggregated) {
            $this->_aggregateFiles($this->_fileList, $theme);
            $this->_isAggregated = true;
        }
        return $this->_fileList->getAll();
    }

    /**
     * Aggregate layout files from modules and a theme and its ancestors, placing results into the list
     *
     * @param Mage_Core_Model_Layout_File_List $list
     * @param Mage_Core_Model_ThemeInterface $theme
     */
    protected function _aggregateFiles(Mage_Core_Model_Layout_File_List $list, Mage_Core_Model_ThemeInterface $theme)
    {
        $list->add($this->_baseFiles->getFiles($theme));
        foreach ($this->_getInheritedThemes($theme) as $theme) {
            $list->add($this->_themeFiles->getFiles($theme));
            $list->replace($this->_overridingBaseFiles->getFiles($theme));
            $list->replace($this->_overridingThemeFiles->getFiles($theme));
        }
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
