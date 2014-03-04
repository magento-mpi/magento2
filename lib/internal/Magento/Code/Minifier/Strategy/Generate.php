<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Minification strategy that generates minified file, if it does not exist or outdated
 */
namespace Magento\Code\Minifier\Strategy;

use Magento\Filesystem\Directory\Read,
    Magento\Filesystem\Directory\Write;

class Generate implements \Magento\Code\Minifier\StrategyInterface
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
    protected $staticViewDir;

    /**
     * @param \Magento\Code\Minifier\AdapterInterface $adapter
     * @param \Magento\App\Filesystem $filesystem
     */
    public function __construct(
        \Magento\Code\Minifier\AdapterInterface $adapter,
        \Magento\App\Filesystem $filesystem
    ) {
        $this->adapter = $adapter;
        $this->rootDirectory = $filesystem->getDirectoryRead(\Magento\App\Filesystem::ROOT_DIR);
        $this->staticViewDir = $filesystem->getDirectoryWrite(\Magento\App\Filesystem::STATIC_VIEW_DIR);
    }

    /**
     * Get path to minified file for specified original file
     *
     * @param string $originalFile path to original file relative to pub/static
     * @param string $targetFile path relative to pub/static
     * @return void
     */
    public function minifyFile($originalFile, $targetFile)
    {
        if ($this->_isUpdateNeeded($originalFile, $targetFile)) {
            $content = $this->rootDirectory->readFile($originalFile);
            $content = $this->adapter->minify($content);
            $targetFile = $this->staticViewDir->getRelativePath($targetFile);
            $this->staticViewDir->writeFile($targetFile, $content);
            $this->staticViewDir->touch($targetFile, $this->rootDirectory->stat($originalFile)['mtime']);
        }
    }

    /**
     * Check whether minified file should be created/updated
     *
     * @param string $originalFile path to original file relative to pub/static
     * @param string $minifiedFile path relative to pub/static
     * @return bool
     */
    protected function _isUpdateNeeded($originalFile, $minifiedFile)
    {
        if (!$this->staticViewDir->isExist($minifiedFile)) {
            return true;
        }
        $originalFileMtime = $this->rootDirectory->stat($originalFile)['mtime'];
        $minifiedFileMtime = $this->staticViewDir->stat($minifiedFile)['mtime'];
        return ($originalFileMtime != $minifiedFileMtime);
    }
}
