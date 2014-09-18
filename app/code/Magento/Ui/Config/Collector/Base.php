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
use Magento\Framework\Filesystem\Directory\ReadInterface;

/**
 * Class Base
 */
class Base implements CollectorInterface
{
    /**
     * File base path
     */
    const BASE_PATH = '/view/base/';

    /**
     * File factory
     *
     * @var Factory
     */
    protected $fileFactory;

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
    public function __construct(Filesystem $filesystem, Factory $fileFactory, $subDir = '')
    {
        $this->modulesDirectory = $filesystem->getDirectoryRead(Filesystem::MODULES_DIR);
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
        $result = [];
        $files = $this->modulesDirectory->search('*/*' . static::BASE_PATH . "{$this->subDir}{$filePath}");
        $filePathPtn = strtr(preg_quote($filePath), ['\*' => '[^/]+']);
        $pattern = "#(?<namespace>[^/]+)/(?<module>[^/]+)/view/base/{$this->subDir}{$filePathPtn}$#i";
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
