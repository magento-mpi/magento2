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

class Generate implements \Magento\Code\Minifier\StrategyInterface
{
    /**
     * @var \Magento\Code\Minifier\AdapterInterface
     */
    protected $_adapter;

    /**
     * @var \Magento\Filesystem
     */
    protected $_filesystem;

    /**
     * @param \Magento\Code\Minifier\AdapterInterface $adapter
     * @param \Magento\Filesystem $filesystem
     */
    public function __construct(
        \Magento\Code\Minifier\AdapterInterface $adapter,
        \Magento\Filesystem $filesystem
    ) {
        $this->_adapter = $adapter;
        $this->_filesystem = $filesystem;
        $this->_filesystem->setIsAllowCreateDirectories(true);
    }

    /**
     * Get path to minified file for specified original file
     *
     * @param string $originalFile path to original file
     * @param string $targetFile
     */
    public function minifyFile($originalFile, $targetFile)
    {
        if ($this->_isUpdateNeeded($originalFile, $targetFile)) {
            $content = $this->_filesystem->read($originalFile);
            $content = $this->_adapter->minify($content);
            $this->_filesystem->write($targetFile, $content);
            $this->_filesystem->touch($targetFile, $this->_filesystem->getMTime($originalFile));
        }
    }

    /**
     * Check whether minified file should be created/updated
     *
     * @param string $originalFile
     * @param string $minifiedFile
     * @return bool
     */
    protected function _isUpdateNeeded($originalFile, $minifiedFile)
    {
        return !$this->_filesystem->has($minifiedFile)
            || ($this->_filesystem->getMTime($originalFile) != $this->_filesystem->getMTime($minifiedFile));
    }
}
