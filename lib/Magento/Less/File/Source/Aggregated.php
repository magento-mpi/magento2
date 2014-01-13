<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Less\File\Source;

use Magento\Less\File\SourceInterface;
use Magento\Less\File\FileList\Factory;
use Magento\View\Design\ThemeInterface;

/**
 * Source of layout files aggregated from a theme and its parents according to merging and overriding conventions
 */
class Aggregated implements SourceInterface
{
    /**
     * @var Factory
     */
    private $fileListFactory;

    /**
     * @var SourceInterface
     */
    private $baseFiles;

    /**
     * @var SourceInterface
     */
    private $themeFiles;

    /**
     * @param Factory $fileListFactory
     * @param SourceInterface $libraryFiles
     * @param SourceInterface $baseFiles
     * @param SourceInterface $themeFiles
     */
    public function __construct(
        Factory $fileListFactory,
        SourceInterface $libraryFiles,
        SourceInterface $baseFiles,
        SourceInterface $themeFiles
    ) {
        $this->fileListFactory = $fileListFactory;
        $this->libraryFiles = $libraryFiles;
        $this->baseFiles = $baseFiles;
        $this->themeFiles = $themeFiles;
    }

    /**
     * Retrieve files
     *
     * Aggregate LESS files from modules and a theme and its ancestors
     *
     * @param string $filePath
     * @param ThemeInterface $theme
     * @return \Magento\View\Layout\File[]
     */
    public function getFiles($filePath = '*', ThemeInterface $theme = null)
    {
        $list = $this->fileListFactory->create();
        $list->add($this->libraryFiles->getFiles($filePath, null));
        $list->add($this->baseFiles->getFiles($filePath, $theme));

        foreach ($this->getInheritedThemes($theme) as $currentTheme) {
            $files = $this->themeFiles->getFiles($filePath, $currentTheme);
            $list->override($files);
        }
        
        return $list->getAll();
    }

    /**
     * Return the full theme inheritance sequence, from the root theme till a specified one
     *
     * @param ThemeInterface $theme
     * @return ThemeInterface[] Format: array([<root_theme>, ..., <parent_theme>,] <current_theme>)
     */
    protected function getInheritedThemes(ThemeInterface $theme)
    {
        $result = array();
        while ($theme) {
            $result[] = $theme;
            $theme = $theme->getParentTheme();
        }
        return array_reverse($result);
    }
}


//TODO: Current class is similar to \Magento\View\Layout\File\Source\Aggregated
