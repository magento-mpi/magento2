<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Css\PreProcessor;

use \Magento\View\Asset\PreProcessor\PreProcessorInterface;

/**
 * Css pre-processor url resolver
 */
class UrlResolver implements PreProcessorInterface
{
    /**
     */
    public function __construct(
        \Magento\View\FileSystem $viewFileSystem,
        \Magento\Logger $logger
    ) {
        $this->viewFileSystem = $viewFileSystem;
        $this->logger = $logger;
    }

    /**
     * Process LESS file content
     *
     * @param \Magento\View\Publisher\FileInterface $publisherFile
     * @param \Magento\Filesystem\Directory\WriteInterface $targetDirectory
     * @return string
     */
    public function process(\Magento\View\Publisher\FileInterface $publisherFile, $targetDirectory)
    {
        return $publisherFile->getSourcePath();
    }
}
