<?php
/**
 * Source of non-overriding layout files introduced by a theme
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
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
            "{$themePath}/{$namespace}_{$module}/layout/{$filePath}.xml"
        );
        $result = array();
        foreach ($files as $filename) {
            $moduleDir = dirname(dirname($filename));
            $moduleFull = basename($moduleDir);
            $result[] = $this->fileFactory->create($filename, $moduleFull, $theme);
        }
        return $result;
    }
}
