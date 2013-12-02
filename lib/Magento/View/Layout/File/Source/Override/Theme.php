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
use Magento\App\Dir;
use Magento\Filesystem;
use Magento\View\Layout\File\Factory;
use Magento\Exception;

/**
 * Source of layout files that explicitly override files of ancestor themes
 */
class Theme implements SourceInterface
{
    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @var Dir
     */
    private $dirs;

    /**
     * @var Factory
     */
    private $fileFactory;

    /**
     * @param Filesystem $filesystem
     * @param Dir $dirs
     * @param Factory $fileFactory
     */
    public function __construct(
        Filesystem $filesystem,
        Dir $dirs,
        Factory $fileFactory
    ) {
        $this->filesystem = $filesystem;
        $this->dirs = $dirs;
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
        $files = $this->filesystem->searchKeys(
            $this->dirs->getDir(Dir::THEMES),
            "{$themePath}/{$namespace}_{$module}/layout/override/theme/*/{$filePath}.xml"
        );

        if (empty($files)) {
            return array();
        }

        $themes = array();
        $currentTheme = $theme;
        while ($currentTheme = $currentTheme->getParentTheme()) {
            $themes[$currentTheme->getCode()] = $currentTheme;
        }

        $result = array();
        $pattern = "#(?<module>[^/]+)/layout/override/theme/(?<themeName>[^/]+)/"
            . preg_quote(rtrim($filePath, '*'))
            . "[^/]*\.xml$#i";
        foreach ($files as $filename) {
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
