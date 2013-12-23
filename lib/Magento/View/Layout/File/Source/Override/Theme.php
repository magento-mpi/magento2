<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\View\Layout\File\Source\Override;

use Magento\View\Layout\File\SourceInterface;
use Magento\View\Design\ThemeInterface;
use Magento\Filesystem;
use Magento\Filesystem\Directory\ReadInterface;
use Magento\View\Layout\File\Factory;
use Magento\Exception;

/**
 * Source of layout files that explicitly override files of ancestor themes
 */
class Theme implements SourceInterface
{
    /**
     * @var ReadInterface
     */
    protected $themesDirectory;

    /**
     * @var Factory
     */
    private $fileFactory;

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
     * @param ThemeInterface $theme
     * @param string $filePath
     * @return array|\Magento\View\Layout\File[]
     * @throws \Magento\Exception
     */
    public function getFiles(ThemeInterface $theme, $filePath = '*')
    {
        $namespace = $module = '*';
        $themePath = $theme->getFullPath();
        $searchPattern = "{$themePath}/{$namespace}_{$module}/layout/override/theme/*/{$filePath}.xml";
        $files = $this->themesDirectory->search($searchPattern);

        if (empty($files)) {
            return array();
        }

        $themes = array();
        $currentTheme = $theme;
        while ($currentTheme = $currentTheme->getParentTheme()) {
            $themes[$currentTheme->getCode()] = $currentTheme;
        }
        $result = array();
        $pattern = "#/(?<module>[^/]+)/layout/override/theme/(?<themeName>[^/]+)/"
            . strtr(preg_quote($filePath), array('\*' => '[^/]+'))
            . "\.xml$#i";
        foreach ($files as $file) {
            $filename = $this->themesDirectory->getAbsolutePath($file);
            if (!preg_match($pattern, $filename, $matches)) {
                continue;
            }
            $moduleFull = $matches['module'];
            $ancestorThemeCode = $matches['themeName'];
            if (!isset($themes[$ancestorThemeCode])) {
                throw new Exception(
                    sprintf(
                        "Trying to override layout file '%s' for theme '%s', which is not ancestor of theme '%s'",
                        $filename,
                        $ancestorThemeCode,
                        $theme->getCode()
                    )
                );
            }
            $result[] = $this->fileFactory->create($filename, $moduleFull, $themes[$ancestorThemeCode]);
        }
        return $result;
    }
}
