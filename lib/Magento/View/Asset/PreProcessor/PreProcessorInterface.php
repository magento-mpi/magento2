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
 * and return the path to the published file (as $sourcePath).
 */
interface PreProcessorInterface
{
    /**
     * @param string $filePath
     * @param array $params
     * @param \Magento\Filesystem\Directory\WriteInterface $targetDirectory
     * @param string|null $sourcePath
     * @return string
     */
    public function process($filePath, $params, $targetDirectory, $sourcePath = null);
}
