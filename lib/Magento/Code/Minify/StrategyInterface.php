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
interface Magento_Code_Minify_StrategyInterface
{
    /**
     * Get path to minified file
     *
     * @param string $originalFile
     * @param Magento_Code_Minifier $minifier
     * @return string
     */
    public function getMinifiedFile($originalFile, Magento_Code_Minifier $minifier);
}
