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
namespace Magento\View\Layout\File\Source;

use Magento\View\Layout\File\SourceInterface;
use Magento\Core\Model\ThemeInterface;
use Magento\Core\Model\Dir;
use Magento\Filesystem;
use Magento\View\Layout\File\FileList\Factory;

class Aggregated implements SourceInterface
{
    /**
     * @var Factory
     */
    private $_fileListFactory;

    /**
     * @var SourceInterface
     */
    private $_baseFiles;

    /**
     * @var SourceInterface
     */
    private $_themeFiles;

    /**
     * @var SourceInterface
     */
    private $_overridingBaseFiles;

    /**
     * @var SourceInterface
     */
    private $_overridingThemeFiles;

    /**
     * @param Factory $fileListFactory
     * @param SourceInterface $baseFiles
     * @param SourceInterface $themeFiles
     * @param SourceInterface $overridingBaseFiles
     * @param SourceInterface $overridingThemeFiles
     */
    public function __construct(
        Factory $fileListFactory,
        SourceInterface $baseFiles,
        SourceInterface $themeFiles,
        SourceInterface $overridingBaseFiles,
        SourceInterface $overridingThemeFiles
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
    public function getFiles(ThemeInterface $theme, $filePath = '*')
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
     * @param ThemeInterface $theme
     * @return ThemeInterface[] Format: array([<root_theme>, ..., <parent_theme>,] <current_theme>)
     */
    protected function _getInheritedThemes(ThemeInterface $theme)
    {
        $result = array();
        while ($theme) {
            $result[] = $theme;
            $theme = $theme->getParentTheme();
        }
        return array_reverse($result);
    }
}
