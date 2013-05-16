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
class Magento_Code_Minifier_Strategy_Lite implements Magento_Code_Minifier_StrategyInterface
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
     * @return bool|string
     */
    public function getMinifiedFile($originalFile, $targetFile)
    {
        if ($this->_isUpdateNeeded($targetFile)) {
            $content = $this->_filesystem->read($originalFile);
            $content = $this->_adapter->minify($content);
            $this->_filesystem->write($targetFile, $content);
        }
        return $targetFile;
    }

    /**
     * Check whether minified file should be created
     *
     * @param string $minifiedFile
     * @return bool
     */
    protected function _isUpdateNeeded($minifiedFile)
    {
        return !$this->_filesystem->has($minifiedFile);
    }
}
