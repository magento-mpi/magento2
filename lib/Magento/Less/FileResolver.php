<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

namespace Magento\Less;

/**
 * File resolver for LESS import instructions
 */
class FileResolver
{
    /**
     * @var \Magento\Filesystem
     */
    protected $filesystem;

    /**
     * @var \Magento\View\FileSystem
     */
    protected $viewFileSystem;

    /**
     * @var \Magento\Less\File\SourceInterface
     */
    protected $fileSource;

    /**
     * @var \Magento\View\Design\ThemeInterface
     */
    protected $theme;

    /**
     * @param \Magento\Filesystem $filesystem
     * @param \Magento\View\FileSystem $viewFileSystem
     * @param \Magento\Less\File\SourceInterface $fileSource
     * @param \Magento\View\DesignInterface $design
     * @param \Magento\View\Design\ThemeInterface $theme
     */
    public function __construct(
        \Magento\Filesystem $filesystem,
        \Magento\View\FileSystem $viewFileSystem,
        \Magento\Less\File\SourceInterface $fileSource,
        \Magento\View\DesignInterface $design,
        \Magento\View\Design\ThemeInterface $theme = null
    ) {
        $this->filesystem = $filesystem;
        $this->viewFileSystem = $viewFileSystem;
        $this->fileSource = $fileSource;
        $this->theme = $theme ?: $design->getDesignTheme();
    }

    /**
     * @param string $file
     * @return array
     */
    public function get($file)
    {
        //$theme = $this->_getPhysicalTheme($this->_theme);
        $files = $this->fileSource->getFiles($this->theme, $file);

        return $files;
    }
}
