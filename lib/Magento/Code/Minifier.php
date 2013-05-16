<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Code_Minifier
{
    /**
     * @var Magento_Code_Minifier_StrategyInterface
     */
    private $_strategy;

    /**
     * @var string directory where minified files are saved
     */
    protected $_baseDir;

    /**
     * @param Magento_Code_Minifier_StrategyInterface $strategy
     * @param string $baseDir
     */
    public function __construct(Magento_Code_Minifier_StrategyInterface $strategy, $baseDir)
    {
        $this->_strategy = $strategy;
        $this->_baseDir = $baseDir;
    }

    /**
     * Get path to minified file
     *
     * @param string $originalFile
     * @return bool|string
     */
    public function getMinifiedFile($originalFile)
    {
        if ($this->_isFileMinified($originalFile)) {
            return $originalFile;
        }
        return $this->_strategy->getMinifiedFile($originalFile,
            $this->_baseDir . '/' . $this->_generateMinifiedFileName($originalFile));
    }

    /**
     * Check if file is minified
     *
     * @param string $fileName
     * @return bool
     */
    protected function _isFileMinified($fileName)
    {
        return (bool)preg_match('#.min.\w+$#', $fileName);
    }

    /**
     * Generate name of the minified file
     *
     * @param string $originalFile
     * @return string
     */
    protected function _generateMinifiedFileName($originalFile)
    {
        $fileInfo = pathinfo($originalFile);
        $minifiedName = md5($originalFile) . '_' . $fileInfo['filename'] . '.min.' . $fileInfo['extension'];

        return $minifiedName;
    }
}
