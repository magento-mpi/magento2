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
     * @param \Magento\View\Publisher\FileInterface $publisherFile
     * @param \Magento\Filesystem\Directory\WriteInterface $targetDirectory
     * @return \Magento\View\Publisher\FileInterface
     */
    public function process(\Magento\View\Publisher\FileInterface $publisherFile, $targetDirectory);
}
