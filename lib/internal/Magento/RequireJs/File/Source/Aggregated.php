<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\RequireJs\File\Source;

use Magento\View\Design\ThemeInterface;
use Magento\View\File\SourceInterface;

/**
 * Source of RequireJs config files basing on list of directories they may be located in
 */
class Aggregated implements SourceInterface
{
    /**
     * Base files
     *
     * @var \Magento\View\File\SourceInterface
     */
    protected $baseFiles;

    /**
     * Theme files
     *
     * @var \Magento\View\File\SourceInterface
     */
    protected $themeFiles;

    /**
     * Theme modular files
     *
     * @var SourceInterface
     */
    protected $themeModularFiles;

    /**
     * @var \Magento\Filesystem\Directory\ReadInterface
     */
    protected $libDirectory;

    /**
     * @var \Magento\View\File\Factory
     */
    protected $fileFactory;

    /**
     * @param \Magento\App\Filesystem $filesystem
     * @param \Magento\View\File\Factory $fileFactory
     * @param \Magento\View\File\SourceInterface $baseFiles
     * @param SourceInterface $themeFiles
     * @param \Magento\View\File\SourceInterface $themeModularFiles
     */
    public function __construct(
        \Magento\App\Filesystem $filesystem,
        \Magento\View\File\Factory $fileFactory,
        SourceInterface $baseFiles,
        SourceInterface $themeFiles,
        SourceInterface $themeModularFiles
    ) {
        $this->libDirectory = $filesystem->getDirectoryRead(\Magento\App\Filesystem::LIB_WEB);
        $this->fileFactory = $fileFactory;
        $this->baseFiles = $baseFiles;
        $this->themeFiles = $themeFiles;
        $this->themeModularFiles = $themeModularFiles;
    }

    /**
     * Get layout files from modules, theme with ancestors and library
     *
     * @param ThemeInterface $theme
     * @param string $filePath
     * @throws \InvalidArgumentException
     * @return \Magento\View\File[]
     */
    public function getFiles(ThemeInterface $theme, $filePath = '')
    {
        if (empty($filePath)) {
            throw new \InvalidArgumentException('File path must be specified');
        }
        $filename = $this->libDirectory->getAbsolutePath($filePath);
        $files = array($this->fileFactory->create($filename, null));

        $files = array_merge($files, $this->baseFiles->getFiles($theme, $filePath));

        foreach ($theme->getInheritedThemes() as $currentTheme) {
            $files = array_merge($files, $this->themeModularFiles->getFiles($currentTheme, $filePath));
            $files = array_merge($files, $this->themeFiles->getFiles($currentTheme, $filePath));
        }
        return $files;
    }
}
