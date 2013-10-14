<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Source of non-overriding layout files introduced by a theme
 */
namespace Magento\View\Layout\File\Source;

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
            "{$themePath}/{$namespace}_{$module}/layout/{$filePath}.xml"
        );
        $result = array();
        foreach ($files as $filename) {
            $moduleDir = dirname(dirname($filename));
            $moduleFull = basename($moduleDir);
            $result[] = $this->_fileFactory->create($filename, $moduleFull, $theme);
        }
        return $result;
    }
}
