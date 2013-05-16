<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Interface for minification strategies
 */
interface Magento_Code_Minifier_StrategyInterface
{
    /**
     * Get path to minified file
     *
     * @param string $originalFile
     * @param string $targetFile
     * @return string
     */
    public function getMinifiedFile($originalFile, $targetFile);
}
