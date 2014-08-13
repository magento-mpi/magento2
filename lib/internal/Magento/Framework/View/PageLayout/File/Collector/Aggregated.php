<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Framework\View\PageLayout\File\Collector;

use Magento\Framework\View\File\CollectorInterface;
use Magento\Framework\View\Design\ThemeInterface;
use Magento\Framework\View\File\FileList\Factory;

/**
 * Source of layout files aggregated from a theme and its parents according to merging and overriding conventions
 */
class Aggregated implements CollectorInterface
{
    /**
     * File list factory
     *
     * @var Factory
     */
    protected $fileListFactory;

    /**
     * Base files
     *
     * @var CollectorInterface
     */
    protected $baseFiles;

    /**
     * Theme files
     *
     * @var CollectorInterface
     */
    protected $themeFiles;

    /**
     * Overridden base files
     *
     * @var CollectorInterface
     */
    protected $overrideBaseFiles;

    /**
     * Overridden theme files
     *
     * @var \Magento\Framework\View\File\CollectorInterface
     */
    protected $overrideThemeFiles;

    /**
     * @param Factory $fileListFactory
     * @param CollectorInterface $baseFiles
     * @param CollectorInterface $themeFiles
     * @param CollectorInterface $overrideBaseFiles
     * @param CollectorInterface $overrideThemeFiles
     */
    public function __construct(
        Factory $fileListFactory,
        CollectorInterface $baseFiles,
        CollectorInterface $themeFiles,
        CollectorInterface $overrideBaseFiles,
        CollectorInterface $overrideThemeFiles
    ) {
        $this->fileListFactory = $fileListFactory;
        $this->baseFiles = $baseFiles;
        $this->themeFiles = $themeFiles;
        $this->overrideBaseFiles = $overrideBaseFiles;
        $this->overrideThemeFiles = $overrideThemeFiles;
    }

    /**
     * Retrieve files
     *
     * Aggregate layout files from modules and a theme and its ancestors
     *
     * @param ThemeInterface $theme
     * @param string $filePath
     * @return \Magento\Framework\View\File[]
     */
    public function getFiles(ThemeInterface $theme, $filePath)
    {
        $list = $this->fileListFactory->create();
        $list->add($this->baseFiles->getFiles($theme, $filePath));

        foreach ($theme->getInheritedThemes() as $currentTheme) {
            $list->add($this->themeFiles->getFiles($currentTheme, $filePath));
            $list->replace($this->overrideBaseFiles->getFiles($currentTheme, $filePath));
            $list->replace($this->overrideThemeFiles->getFiles($currentTheme, $filePath));
        }
        return $list->getAll();
    }

    /**
     * @param ThemeInterface $theme
     * @param string $filePath
     * @return array
     */
    public function getFilesContent(ThemeInterface $theme, $filePath)
    {
        $result = [];
        foreach ($this->getFiles($theme, $filePath) as $file) {
            $result[$file->getFilename()] = file_get_contents($file->getFilename());
        }

        return $result;
    }
}
