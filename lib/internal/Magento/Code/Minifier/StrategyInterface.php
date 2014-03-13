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
     * @param string $originalFile Path to the file to be minified. Relative to root directory.
     * @param string $targetFile Path for minified content. Relative to static view directory.
     *
     * @return void
     */
    public function minifyFile($originalFile, $targetFile);
}
