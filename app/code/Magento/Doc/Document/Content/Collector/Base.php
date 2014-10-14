<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Doc\Document\Content\Collector;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\View\File\CollectorInterface;
use Magento\Framework\View\Design\ThemeInterface;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Directory\ReadInterface;
use Magento\Framework\View\File\Factory;

/**
 * Class Base
 * @package Magento\Doc\Document\Content\Collector
 */
class Base implements CollectorInterface
{
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
     * @param Factory $fileFactory
     * @param string $subDir
     */
    public function __construct(
        Filesystem $filesystem,
        Factory $fileFactory,
        $subDir = ''
    ) {
        $this->modulesDirectory = $filesystem->getDirectoryRead(DirectoryList::MODULES);
        $this->fileFactory = $fileFactory;
        $this->subDir = $subDir ? $subDir . '/' : '';
    }

    /**
     * Retrieve files
     *
     * @param ThemeInterface $theme
     * @param string $filePath
     * @return array|\Magento\Framework\View\File[]
     */
    public function getFiles(ThemeInterface $theme, $filePath)
    {
        $result = [];
        $files = $this->modulesDirectory->search("*/*/docs/content/{$this->subDir}{$filePath}");

        $filePathPtn = strtr(preg_quote($filePath), array('\*' => '[^/]+'));
        $pattern = "#(?<namespace>[^/]+)/(?<module>[^/]+)/docs/content/{$this->subDir}{$filePathPtn}$#i";
        foreach ($files as $file) {
            $filename = $this->modulesDirectory->getAbsolutePath($file);
            if (!preg_match($pattern, $filename, $matches)) {
                continue;
            }
            $moduleFull = "{$matches['namespace']}_{$matches['module']}";
            $result[] = $this->fileFactory->create($filename, $moduleFull, null, true);
        }
        return $result;
    }
}
