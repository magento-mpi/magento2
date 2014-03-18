<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\RequireJs\Config\File\Collector;

use Magento\View\Design\ThemeInterface;
use Magento\View\File\CollectorInterface;

/**
 * Source of RequireJs config files basing on list of directories they may be located in
 */
class Aggregated implements CollectorInterface
{
    /**
     * Base files
     *
     * @var \Magento\View\File\CollectorInterface
     */
    protected $baseFiles;

    /**
     * Theme files
     *
     * @var \Magento\View\File\CollectorInterface
     */
    protected $themeFiles;

    /**
     * Theme modular files
     *
     * @var CollectorInterface
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
     * @param CollectorInterface $baseFiles
     * @param CollectorInterface $themeFiles
     * @param CollectorInterface $themeModularFiles
     */
    public function __construct(
        \Magento\App\Filesystem $filesystem,
        \Magento\View\File\Factory $fileFactory,
        CollectorInterface $baseFiles,
        CollectorInterface $themeFiles,
        CollectorInterface $themeModularFiles
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
    public function getFiles(ThemeInterface $theme, $filePath)
    {
        if (empty($filePath)) {
            throw new \InvalidArgumentException('File path must be specified');
        }
        $files = array();
        if ($this->libDirectory->isExist($filePath)) {
            $filename = $this->libDirectory->getAbsolutePath($filePath);
            $files[] = $this->fileFactory->create($filename);
        }

        $files = array_merge($files, $this->baseFiles->getFiles($theme, $filePath));

        foreach ($theme->getInheritedThemes() as $currentTheme) {
            $files = array_merge($files, $this->themeModularFiles->getFiles($currentTheme, $filePath));
            $files = array_merge($files, $this->themeFiles->getFiles($currentTheme, $filePath));
        }
        return $files;
    }
}
