<?php
/**
 * Source of base layout files introduced by modules
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\View\Layout\File\Source;

use Magento\View\Layout\File\Source;
use Magento\View\Design\Theme;
use Magento\Core\Model\Dir;
use Magento\Filesystem;
use Magento\View\Layout\File\Factory;

class Base implements Source
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
    public function getFiles(Theme $theme, $filePath = '*')
    {
        $namespace = $module = '*';
        $area = $theme->getArea();
        $files = $this->_filesystem->searchKeys(
            $this->_dirs->getDir(Dir::MODULES),
            "{$namespace}/{$module}/view/{$area}/layout/{$filePath}.xml"
        );
        $result = array();
        foreach ($files as $filename) {
            $moduleDir = dirname(dirname(dirname(dirname($filename))));
            $module = basename($moduleDir);
            $namespace = basename(dirname($moduleDir));
            $moduleFull = "{$namespace}_{$module}";
            $result[] = $this->_fileFactory->create($filename, $moduleFull);
        }
        return $result;
    }
}
