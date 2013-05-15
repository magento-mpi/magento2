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
class Magento_Code_Minify_Strategy_Generate implements Magento_Code_Minify_StrategyInterface
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

            $minifiedFile = $this->_findOriginalMinifiedFile($originalFile);
            if ($minifiedFile) {
                return $minifiedFile;
            }

            $minifiedFile = $this->_baseDir . '/' . $minifier->generateMinifiedFileName($originalFile);
            if ($this->_isUpdateNeeded($originalFile, $minifiedFile)) {
                $content = $this->_filesystem->read($originalFile);
                $content = $this->_adapter->minify($content);
                $this->_filesystem->write($minifiedFile, $content);
            }

            return $minifiedFile;
        }

        return $originalFile;
    }

    /**
     * Search for minified file provided along with the original file in the code base
     *
     * @param string $originalFile
     * @return bool|string
     */
    protected function _findOriginalMinifiedFile($originalFile)
    {
        $fileInfo = pathinfo($originalFile);
        $minifiedFile = $fileInfo['dirname'] . '/' . $fileInfo['filename'] . '.min.' . $fileInfo['extension'];
        if ($this->_filesystem->has($minifiedFile)) {
            return $minifiedFile;
        }
        return false;
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
            || ($this->_filesystem->getMTime($originalFile) > $this->_filesystem->getMTime($minifiedFile));
    }
}
