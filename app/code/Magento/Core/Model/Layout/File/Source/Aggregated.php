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
namespace Magento\Core\Model\Layout\File\Source;

class Aggregated implements \Magento\Core\Model\Layout\File\SourceInterface
{
    /**
     * @var \Magento\Core\Model\Layout\File\FileList\Factory
     */
    private $_fileListFactory;

    /**
     * @var \Magento\Core\Model\Layout\File\SourceInterface
     */
    private $_baseFiles;

    /**
     * @var \Magento\Core\Model\Layout\File\SourceInterface
     */
    private $_themeFiles;

    /**
     * @var \Magento\Core\Model\Layout\File\SourceInterface
     */
    private $_overridingBaseFiles;

    /**
     * @var \Magento\Core\Model\Layout\File\SourceInterface
     */
    private $_overridingThemeFiles;

    /**
     * @param \Magento\Core\Model\Layout\File\FileList\Factory $fileListFactory
     * @param \Magento\Core\Model\Layout\File\SourceInterface $baseFiles
     * @param \Magento\Core\Model\Layout\File\SourceInterface $themeFiles
     * @param \Magento\Core\Model\Layout\File\SourceInterface $overridingBaseFiles
     * @param \Magento\Core\Model\Layout\File\SourceInterface $overridingThemeFiles
     */
    public function __construct(
        \Magento\Core\Model\Layout\File\FileList\Factory $fileListFactory,
        \Magento\Core\Model\Layout\File\SourceInterface $baseFiles,
        \Magento\Core\Model\Layout\File\SourceInterface $themeFiles,
        \Magento\Core\Model\Layout\File\SourceInterface $overridingBaseFiles,
        \Magento\Core\Model\Layout\File\SourceInterface $overridingThemeFiles
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
    public function getFiles(\Magento\Core\Model\ThemeInterface $theme)
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
     * @param \Magento\Core\Model\ThemeInterface $theme
     * @return \Magento\Core\Model\ThemeInterface[] Format: array([<root_theme>, ..., <parent_theme>,] <current_theme>)
     */
    protected function _getInheritedThemes(\Magento\Core\Model\ThemeInterface $theme)
    {
        $result = array();
        while ($theme) {
            $result[] = $theme;
            $theme = $theme->getParentTheme();
        }
        return array_reverse($result);
    }
}
