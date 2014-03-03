<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\View\Asset;

/**
 * An interface for "preprocessing" asset contents
 */
interface PreProcessorInterface
{
    /**
     * Transform content and/or content type for the specified asset
     *
     * The $content and $contentType are data that's known about this asset so far. This pre-processor may modify them.
     * Return value must be array of exactly 2 elements: array($content, $contentType)
     * Where the return values may be the same or modified, depending on preprocessor implementation
     *
     * @param string $content
     * @param string $contentType
     * @param LocalInterface $asset
     * @return array array($content, $contentType)
     */
    public function process($content, $contentType, LocalInterface $asset);
} 
