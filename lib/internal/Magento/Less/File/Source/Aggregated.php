<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Less\File\Source;

use Magento\View\File\SourceInterface;
use Magento\View\Layout\File\FileList\Factory;
use Magento\View\Design\ThemeInterface;

/**
 * Source of layout files aggregated from a theme and its parents according to merging and overriding conventions
 */
class Aggregated implements SourceInterface
{
    /**
     * @var Factory
     */
    protected $fileListFactory;

    /**
     * @var SourceInterface
     */
    protected $libraryFiles;

    /**
     * @var SourceInterface
     */
    protected $baseFiles;

    /**
     * @var SourceInterface
     */
    protected $themeFiles;

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
     * @param \Magento\View\Design\ThemeInterface $theme
     * @param string $filePath
     * @return \Magento\View\File[]
     * @throws \LogicException
     */
    public function getFiles(ThemeInterface $theme, $filePath = '*')
    {
        $list = $this->fileListFactory->create('Magento\Less\File\FileList\Collator');
        $list->add($this->libraryFiles->getFiles($theme, $filePath));
        $list->add($this->baseFiles->getFiles($theme, $filePath));

        foreach ($theme->getInheritedThemes() as $currentTheme) {
            $files = $this->themeFiles->getFiles($currentTheme, $filePath);
            $list->replace($files);
        }
        $result = $list->getAll();
        if (empty($result)) {
            throw new \LogicException('magento_import returns empty result by path ' . $filePath);
        }
        return $result;
    }
}
