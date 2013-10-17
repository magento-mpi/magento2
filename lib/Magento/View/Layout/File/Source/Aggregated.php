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
    private $fileListFactory;

    /**
     * @var Source
     */
    private $baseFiles;

    /**
     * @var Source
     */
    private $themeFiles;

    /**
     * @var Source
     */
    private $overrideBaseFiles;

    /**
     * @var Source
     */
    private $overrideThemeFiles;

    /**
     * @param Factory $fileListFactory
     * @param Source $baseFiles
     * @param Source $themeFiles
     * @param Source $overrideBaseFiles
     * @param Source $overrideThemeFiles
     */
    public function __construct(
        Factory $fileListFactory,
        Source $baseFiles,
        Source $themeFiles,
        Source $overrideBaseFiles,
        Source $overrideThemeFiles
    ) {
        $this->fileListFactory = $fileListFactory;
        $this->baseFiles = $baseFiles;
        $this->themeFiles = $themeFiles;
        $this->overrideBaseFiles = $overrideBaseFiles;
        $this->overrideThemeFiles = $overrideThemeFiles;
    }

    /**
     * Aggregate layout files from modules and a theme and its ancestors
     *
     * {@inheritdoc}
     */
    public function getFiles(Theme $theme, $filePath = '*')
    {
        $list = $this->fileListFactory->create();
        $list->add($this->baseFiles->getFiles($theme, $filePath));

        foreach ($this->getInheritedThemes($theme) as $currentTheme) {
            $list->add($this->themeFiles->getFiles($currentTheme, $filePath));
            $list->replace($this->overrideBaseFiles->getFiles($currentTheme, $filePath));
            $list->replace($this->overrideThemeFiles->getFiles($currentTheme, $filePath));
        }
        return $list->getAll();
    }

    /**
     * Return the full theme inheritance sequence, from the root theme till a specified one
     *
     * @param Theme $theme
     * @return Theme[] Format: array([<root_theme>, ..., <parent_theme>,] <current_theme>)
     */
    protected function getInheritedThemes(Theme $theme)
    {
        $result = array();
        while ($theme) {
            $result[] = $theme;
            $theme = $theme->getParentTheme();
        }
        return array_reverse($result);
    }
}
