<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Ui\Config\Collector;

use Magento\Framework\App\Filesystem;
use Magento\Framework\View\File\Factory;
use Magento\Framework\View\DesignInterface;
use Magento\Framework\Filesystem\Directory\ReadInterface;

/**
 * Class Theme
 */
class Theme implements CollectorInterface
{
    /**
     * Theme
     *
     * @var \Magento\Core\Model\Theme
     */
    protected $theme;

    /**
     * File factory
     *
     * @var Factory
     */
    private $fileFactory;

    /**
     * Modules directory
     *
     * @var ReadInterface
     */
    protected $modulesDirectory;

    /**
     * Subdirectory where the files are located
     *
     * @var string
     */
    protected $subDir;

    /**
     * Constructor
     *
     * @param Filesystem $filesystem
     * @param DesignInterface $design
     * @param Factory $fileFactory
     * @param string $subDir
     */
    public function __construct(Filesystem $filesystem, DesignInterface $design, Factory $fileFactory, $subDir = '')
    {
        $this->themesDirectory = $filesystem->getDirectoryRead(Filesystem::THEMES_DIR);
        $this->theme = $design->getDesignTheme();
        $this->fileFactory = $fileFactory;
        $this->subDir = $subDir !== '' ? $subDir . '/' : '';
    }

    /**
     * Retrieve files
     *
     * @param string $filePath
     * @return array|\Magento\Framework\View\File[]
     */
    public function getFiles($filePath)
    {
        $themePath = $this->theme->getFullPath();
        $files = $this->themesDirectory->search("{$themePath}/{$this->subDir}{$filePath}");
        $result = [];
        foreach ($files as $file) {
            $filename = $this->themesDirectory->getAbsolutePath($file);
            $result[] = $this->fileFactory->create($filename, null, $this->theme);
        }
        return $result;
    }
}
