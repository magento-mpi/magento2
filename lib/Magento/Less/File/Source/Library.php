<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Less\File\Source;

use Magento\Less\File\SourceInterface;
use Magento\View\Design\ThemeInterface;
use Magento\Filesystem;
use Magento\Filesystem\Directory\ReadInterface;
use Magento\View\Layout\File\Factory;

/**
 * Source of base layout files introduced by modules
 */
class Library implements SourceInterface
{
    /**
     * @var Factory
     */
    private $fileFactory;

    /**
     * @var ReadInterface
     */
    protected $libraryDirectory;

    /**
     * @param Filesystem $filesystem
     * @param Factory $fileFactory
     */
    public function __construct(
        Filesystem $filesystem,
        Factory $fileFactory
    ) {
        $this->libraryDirectory = $filesystem->getDirectoryRead(Filesystem::PUB_LIB);
        $this->fileFactory = $fileFactory;
    }

    /**
     * Retrieve files
     *
     * @param string $filePath
     * @param ThemeInterface $theme
     * @return array|\Magento\View\Layout\File[]
     */
    public function getFiles($filePath = '*', ThemeInterface $theme = null)
    {
        $filePath = pathinfo($filePath, PATHINFO_EXTENSION) ? $filePath : rtrim($filePath, '.') . '.less';
        $files = $this->libraryDirectory->search("less/{$filePath}");
        $result = array();
        foreach ($files as $file) {
            $filename = $this->libraryDirectory->getAbsolutePath($file);
            $result[] = $this->fileFactory->create($filename, '');
        }
        return $result;
    }
}
