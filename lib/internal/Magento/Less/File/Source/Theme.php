<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Less\File\Source;

use Magento\View\File\SourceInterface;
use Magento\View\Design\ThemeInterface;
use Magento\App\Filesystem;
use Magento\Filesystem\Directory\ReadInterface;
use Magento\View\File\Factory;

/**
 * Source of non-overriding layout files introduced by a theme
 */
class Theme implements SourceInterface
{
    /**
     * @var Factory
     */
    protected $fileFactory;

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
        $filePath = pathinfo($filePath, PATHINFO_EXTENSION) ? $filePath : rtrim($filePath, '.') . '.less';

        $namespace = $module = '*';
        $themePath = $theme->getFullPath();
        $files = $this->themesDirectory->search("{$themePath}/{$namespace}_{$module}/{$filePath}");
        $result = array();
        $pattern = "#/(?<moduleName>[^/]+)/" . strtr(preg_quote($filePath), array('\*' => '[^/]+')) . "#i";
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
