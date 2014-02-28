<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\RequireJs\File\Source;

use Magento\View\File\SourceInterface;
use Magento\View\Design\ThemeInterface;
use Magento\App\Filesystem;
use Magento\Filesystem\Directory\ReadInterface;
use Magento\View\File\Factory;

/**
 * Source of RequireJs config files in root directory of the theme or its ancestor themes
 */
class Theme implements SourceInterface
{
    /**
     * File factory
     *
     * @var Factory
     */
    private $fileFactory;

    /**
     * Themes directory
     *
     * @var ReadInterface
     */
    protected $themesDirectory;

    /**
     * Constructor
     *
     * @param Filesystem $filesystem
     * @param \Magento\View\File\Factory $fileFactory
     */
    public function __construct(
        Filesystem $filesystem,
        Factory $fileFactory
    ) {
        $this->themesDirectory = $filesystem->getDirectoryRead(Filesystem::THEMES_DIR);
        $this->fileFactory = $fileFactory;
    }

    /**
     * Retrieve files
     *
     * @param ThemeInterface $theme
     * @param string $filePath
     * @return array|\Magento\View\File[]
     */
    public function getFiles(ThemeInterface $theme, $filePath = '*')
    {
        $themePath = $theme->getFullPath();
        $files = $this->themesDirectory->search("{$themePath}/$filePath");
        $result = array();
        foreach ($files as $file) {
            $filename = $this->themesDirectory->getAbsolutePath($file);
            $result[] = $this->fileFactory->create($filename, null, $theme);
        }
        return $result;
    }
}
