<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Source of base layout files introduced by modules
 */
namespace Magento\Core\Model\Layout\File\Source;

class Base implements \Magento\Core\Model\Layout\File\SourceInterface
{
    /**
     * @var \Magento\Filesystem
     */
    private $_filesystem;

    /**
     * @var \Magento\Core\Model\Dir
     */
    private $_dirs;

    /**
     * @var \Magento\Core\Model\Layout\File\Factory
     */
    private $_fileFactory;

    /**
     * @param \Magento\Filesystem $filesystem
     * @param \Magento\Core\Model\Dir $dirs
     * @param \Magento\Core\Model\Layout\File\Factory $fileFactory
     */
    public function __construct(
        \Magento\Filesystem $filesystem,
        \Magento\Core\Model\Dir $dirs,
        \Magento\Core\Model\Layout\File\Factory $fileFactory
    ) {
        $this->_filesystem = $filesystem;
        $this->_dirs = $dirs;
        $this->_fileFactory = $fileFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function getFiles(\Magento\Core\Model\ThemeInterface $theme)
    {
        $namespace = $module = '*';
        $area = $theme->getArea();
        $files = $this->_filesystem->searchKeys(
            $this->_dirs->getDir(\Magento\Core\Model\Dir::MODULES),
            "{$namespace}/{$module}/view/{$area}/layout/*.xml"
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
