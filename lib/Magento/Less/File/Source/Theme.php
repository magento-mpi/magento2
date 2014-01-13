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
 * Source of non-overriding layout files introduced by a theme
 */
class Theme implements SourceInterface
{
    /**
     * @var Factory
     */
    private $fileFactory;

    /**
     * @var ReadInterface
     */
    protected $themesDirectory;

    /**
     * @param Filesystem $filesystem
     * @param Factory $fileFactory
     */
    public function __construct(
        Filesystem $filesystem,
        Factory $fileFactory
    ) {
        $this->themesDirectory = $filesystem->getDirectoryRead(Filesystem::THEMES);
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

        $namespace = $module = '*';
        $themePath = $theme->getFullPath();
        $files = $this->themesDirectory->search("{$themePath}/{$namespace}_{$module}/less/{$filePath}");
        $result = array();
        $pattern = "#/(?<moduleName>[^/]+)/less/" . strtr(preg_quote($filePath), array('\*' => '[^/]+')) . "#i";
        foreach ($files as $file) {
            $filename = $this->themesDirectory->getAbsolutePath($file);
            if (!preg_match($pattern, $filename, $matches)) {
                continue;
            }
            $result[] = $this->fileFactory->create($filename, $matches['moduleName'], null);
        }

        return $result;
    }
}
