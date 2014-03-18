<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\View\File\Collector;

use Magento\View\File\CollectorInterface;
use Magento\View\Design\ThemeInterface;
use Magento\App\Filesystem;
use Magento\Filesystem\Directory\ReadInterface;
use Magento\View\File\Factory;

/**
 * Source of modular view files introduced by a theme
 */
class ThemeModular implements CollectorInterface
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
     * @var string
     */
    protected $subDir;

    /**
     * Constructor
     *
     * @param Filesystem $filesystem
     * @param Factory $fileFactory
     * @param string $subDir
     */
    public function __construct(
        Filesystem $filesystem,
        Factory $fileFactory,
        $subDir = ''
    ) {
        $this->themesDirectory = $filesystem->getDirectoryRead(Filesystem::THEMES_DIR);
        $this->fileFactory = $fileFactory;
        $this->subDir = $subDir ? $subDir . '/' : '';
    }

    /**
     * Retrieve files
     *
     * @param ThemeInterface $theme
     * @param string $filePath
     * @return array|\Magento\View\File[]
     */
    public function getFiles(ThemeInterface $theme, $filePath)
    {
        $namespace = $module = '*';
        $themePath = $theme->getFullPath();
        $files = $this->themesDirectory->search("{$themePath}/{$namespace}_{$module}/{$this->subDir}{$filePath}");
        $result = array();
        $pattern = "#/(?<moduleName>[^/]+)/{$this->subDir}"
            . strtr(preg_quote($filePath), array('\*' => '[^/]+')) . "$#i";
        foreach ($files as $file) {
            $filename = $this->themesDirectory->getAbsolutePath($file);
            if (!preg_match($pattern, $filename, $matches)) {
                continue;
            }
            $result[] = $this->fileFactory->create($filename, $matches['moduleName'], $theme);
        }
        return $result;
    }
}
