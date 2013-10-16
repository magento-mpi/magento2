<?php
/**
 * Source of layout files aggregated from a theme and its parents according to merging and overriding conventions
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\View\Layout\File\Source;

use Magento\View\Layout\File\Source;
use Magento\View\Design\Theme;
use Magento\Core\Model\Dir;
use Magento\Filesystem;
use Magento\View\Layout\File\FileList\Factory;

class Aggregated implements Source
{
    /**
     * @var Factory
     */
    private $_fileListFactory;

    /**
     * @var Source
     */
    private $_baseFiles;

    /**
     * @var Source
     */
    private $_themeFiles;

    /**
     * @var Source
     */
    private $_overridingBaseFiles;

    /**
     * @var Source
     */
    private $_overridingThemeFiles;

    /**
     * @param Factory $fileListFactory
     * @param Source $baseFiles
     * @param Source $themeFiles
     * @param Source $overridingBaseFiles
     * @param Source $overridingThemeFiles
     */
    public function __construct(
        Factory $fileListFactory,
        Source $baseFiles,
        Source $themeFiles,
        Source $overridingBaseFiles,
        Source $overridingThemeFiles
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
    public function getFiles(Theme $theme, $filePath = '*')
    {
        $list = $this->_fileListFactory->create();
        $list->add($this->_baseFiles->getFiles($theme, $filePath));

        foreach ($this->_getInheritedThemes($theme) as $currentTheme) {
            $list->add($this->_themeFiles->getFiles($currentTheme, $filePath));
            $list->replace($this->_overridingBaseFiles->getFiles($currentTheme, $filePath));
            $list->replace($this->_overridingThemeFiles->getFiles($currentTheme, $filePath));
        }
        return $list->getAll();
    }

    /**
     * Return the full theme inheritance sequence, from the root theme till a specified one
     *
     * @param Theme $theme
     * @return Theme[] Format: array([<root_theme>, ..., <parent_theme>,] <current_theme>)
     */
    protected function _getInheritedThemes(Theme $theme)
    {
        $result = array();
        while ($theme) {
            $result[] = $theme;
            $theme = $theme->getParentTheme();
        }
        return array_reverse($result);
    }
}
