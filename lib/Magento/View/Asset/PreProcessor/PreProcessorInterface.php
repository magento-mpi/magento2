<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\View\Asset\PreProcessor;

/**
 * The pre-processors which implement this interface are supposed to publish processed content to the $targetDirectory
 * and return the path to the published file.
 */
interface PreProcessorInterface
{
    /**
     * Process content
     *
     * @param string $filePath
     * @param array $params
     * @param null|string $content
     * @param string|null $sourcePath
     * @return string
     */
    public function process($filePath, $params, $content, $sourcePath = null);
}
