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
class Magento_Code_Minifier_Strategy_Generate implements Magento_Code_Minifier_StrategyInterface
{
    /**
     * @var Magento_Code_Minifier_AdapterInterface
     */
    protected $_adapter;

    /**
     * @var Magento_Filesystem
     */
    protected $_filesystem;

    /**
     * @param Magento_Code_Minifier_AdapterInterface $adapter
     * @param Magento_Filesystem $filesystem
     */
    public function __construct(
        Magento_Code_Minifier_AdapterInterface $adapter,
        Magento_Filesystem $filesystem
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
