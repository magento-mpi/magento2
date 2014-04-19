<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\View\Asset\PreProcessor;

/**
 * The pre-processors which implement this interface are supposed to publish processed content to the $targetDirectory
 * and return publisher file object with updated path to the published file.
 */
interface PreProcessorInterface
{
    /**
     * Process content
     *
     * @param \Magento\Framework\View\Publisher\FileInterface $publisherFile
     * @param \Magento\Framework\Filesystem\Directory\WriteInterface $targetDirectory
     * @return \Magento\Framework\View\Publisher\FileInterface
     */
    public function process(\Magento\Framework\View\Publisher\FileInterface $publisherFile, $targetDirectory);
}
