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
     * @var Magento_Code_Minify_StrategyInterface
     */
    private $_strategy;

    /**
     * @param Magento_Code_Minify_StrategyInterface $strategy
     */
    public function __construct(Magento_Code_Minify_StrategyInterface $strategy)
    {
        $this->_strategy = $strategy;
    }

    /**
     * Get path to minified file
     *
     * @param string $originalFile
     * @return bool|string
     */
    public function getMinifiedFile($originalFile)
    {
        return $this->_strategy->getMinifiedFile($originalFile, $this);
    }

    /**
     * Check if file is minified
     *
     * @param string $fileName
     * @return bool
     */
    public function isFileMinified($fileName)
    {
        return (bool)preg_match('#.min.\w+$#', $fileName);
    }

    /**
     * Generate name of the minified file
     *
     * @param string $originalFile
     * @return string
     */
    public function generateMinifiedFileName($originalFile)
    {
        $fileInfo = pathinfo($originalFile);
        $minifiedName = md5($originalFile) . '_' . $fileInfo['filename'] . '.min.' . $fileInfo['extension'];

        return $minifiedName;
    }
}
