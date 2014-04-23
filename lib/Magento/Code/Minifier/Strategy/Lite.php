<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Minification strategy with light-weight operations with file system
 *
 * TODO: eliminate dependency of an adapter and write access to file system
 * TODO: Goal: provide path to existing minified file w/o its creation
 */
namespace Magento\Code\Minifier\Strategy;

use Magento\Framework\Filesystem\Directory\Read;
use Magento\Framework\Filesystem\Directory\Write;

class Lite implements \Magento\Code\Minifier\StrategyInterface
{
    /**
     * @var \Magento\Code\Minifier\AdapterInterface
     */
    protected $adapter;

    /**
     * @var Read
     */
    protected $rootDirectory;

    /**
     * @var Write
     */
    protected $pubViewCacheDir;

    /**
     * @param \Magento\Code\Minifier\AdapterInterface $adapter
     * @param \Magento\Framework\App\Filesystem $filesystem
     */
    public function __construct(\Magento\Code\Minifier\AdapterInterface $adapter, \Magento\Framework\App\Filesystem $filesystem)
    {
        $this->adapter = $adapter;
        $this->rootDirectory = $filesystem->getDirectoryRead(\Magento\Framework\App\Filesystem::ROOT_DIR);
        $this->pubViewCacheDir = $filesystem->getDirectoryWrite(\Magento\Framework\App\Filesystem::PUB_VIEW_CACHE_DIR);
    }

    /**
     * Get path to minified file for specified original file
     *
     * @param string $originalFile path to original file relative to pub/view_cache
     * @param string $targetFile path relative to pub/view_cache
     * @return void
     */
    public function minifyFile($originalFile, $targetFile)
    {
        if ($this->_isUpdateNeeded($targetFile)) {
            $content = $this->rootDirectory->readFile($originalFile);
            $content = $this->adapter->minify($content);
            $this->pubViewCacheDir->writeFile($targetFile, $content);
        }
    }

    /**
     * Check whether minified file should be created
     *
     * @param string $minifiedFile path relative to pub/view_cache
     * @return bool
     */
    protected function _isUpdateNeeded($minifiedFile)
    {
        return !$this->pubViewCacheDir->isExist($minifiedFile);
    }
}
