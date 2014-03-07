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
     * @param string $originalFile Path to the file to be minified
     * @param string $targetRelFile Path relative to pub/static, where minified content should be put
     *
     * @return void
     */
    public function minifyFile($originalFile, $targetRelFile);
}
