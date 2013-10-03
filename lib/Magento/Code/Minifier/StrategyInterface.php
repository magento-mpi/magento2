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
namespace Magento\Code\Minifier;

interface StrategyInterface
{
    /**
     * Generates minified file
     *
     * @param string $originalFile
     * @param string $targetFile
     */
    public function minifyFile($originalFile, $targetFile);
}
