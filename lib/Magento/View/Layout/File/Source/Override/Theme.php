<?php
/**
 * Source of layout files that explicitly override files of ancestor themes
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\View\Layout\File\Source\Override;

use Magento\View\Layout\File\Source;
use Magento\View\Design\Theme as ThemeInterface;
use Magento\Core\Model\Dir;
use Magento\Filesystem;
use Magento\View\Layout\File\Factory;
use Magento\Core\Exception;

class Theme implements Source
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
     * {@inheritdoc}
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
        foreach ($files as $filename) {
            if (preg_match("#([^/]+)/layout/override/theme/([^/]+)/[^/]+\.xml$#i", $filename, $matches)) {
                $moduleFull = $matches[1];
                $ancestorThemeCode = $matches[2];
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
        }
        return $result;
    }
}
