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
     * @param string $originalFile path relative to pub/static
     * @param string $targetFile path relative to pub/static
     *
     * @return void
     */
    public function minifyFile($originalFile, $targetFile);
}
