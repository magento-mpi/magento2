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
class Magento_Code_Minify_Strategy_Lite implements Magento_Code_Minify_StrategyInterface
{
    /**
     * @var Magento_Code_Minify_AdapterInterface
     */
    protected $_adapter;

    /**
     * @var Magento_Filesystem
     */
    protected $_filesystem;

    /**
     * @var string directory where minified files are saved
     */
    protected $_baseDir;

    /**
     * @param Magento_Code_Minify_AdapterInterface $adapter
     * @param Magento_Filesystem $filesystem
     * @param string $baseDir
     */
    public function __construct(
        Magento_Code_Minify_AdapterInterface $adapter,
        Magento_Filesystem $filesystem,
        $baseDir
    ) {
        $this->_adapter = $adapter;
        $this->_filesystem = $filesystem;
        $this->_filesystem->setIsAllowCreateDirectories(true);
        $this->_baseDir = $baseDir;
    }

    /**
     * Get path to minified file for specified original file
     *
     * @param string $originalFile path to original file
     * @param Magento_Code_Minifier $minifier
     * @return bool|string
     */
    public function getMinifiedFile($originalFile, Magento_Code_Minifier $minifier)
    {
        if (!$minifier->isFileMinified($originalFile)) {
            $minifiedFile = $this->_baseDir . '/' . $minifier->generateMinifiedFileName($originalFile);
            if ($this->_isUpdateNeeded($minifiedFile)) {
                $content = $this->_filesystem->read($originalFile);
                $content = $this->_adapter->minify($content);
                $this->_filesystem->write($minifiedFile, $content);
            }
            return $minifiedFile;
        }

        return $originalFile;
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
