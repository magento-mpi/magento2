<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Source of layout files that explicitly override base files introduced by modules
 */
namespace Magento\View\Layout\File\Source\Override;

use Magento\View\Layout\File\SourceInterface;
use Magento\Core\Model\ThemeInterface;
use Magento\Core\Model\Dir;
use Magento\Filesystem;
use Magento\View\Layout\File\Factory;

class Base implements SourceInterface
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
            "{$themePath}/{$namespace}_{$module}/layout/override/base/{$filePath}.xml"
        );

        $result = array();
        foreach ($files as $filename) {
            $moduleDir = dirname(dirname(dirname(dirname($filename))));
            $moduleFull = basename($moduleDir);
            $result[] = $this->_fileFactory->create($filename, $moduleFull);
        }
        return $result;
    }
}
