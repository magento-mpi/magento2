<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Source of layout files that explicitly override files of ancestor themes
 */
namespace Magento\View\Layout\File\Source\Override;

use Magento\View\Layout\File\Source;
use Magento\View\Design\Theme as ThemeInterface;
use Magento\Core\Model\Dir;
use Magento\Filesystem;
use Magento\View\Layout\File\Factory;

class Theme implements Source
{
    /**
     * @var Filesystem
     */
    private $_filesystem;

    /**
     * @var Dir
     */
    private $_dirs;

    /**
     * @var Factory
     */
    private $_fileFactory;

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
        $this->_filesystem = $filesystem;
        $this->_dirs = $dirs;
        $this->_fileFactory = $fileFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function getFiles(ThemeInterface $theme, $filePath = '*')
    {
        $namespace = $module = '*';
        $themePath = $theme->getFullPath();
        $files = $this->_filesystem->searchKeys(
            $this->_dirs->getDir(Dir::THEMES),
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
                    throw new \Magento\Core\Exception(sprintf(
                        "Trying to override layout file '%s' for theme '%s', which is not ancestor of theme '%s'",
                        $filename, $ancestorThemeCode, $theme->getCode()
                    ));
                }
                $result[] = $this->_fileFactory->create($filename, $moduleFull, $themes[$ancestorThemeCode]);
            }
        }
        return $result;
    }
}
