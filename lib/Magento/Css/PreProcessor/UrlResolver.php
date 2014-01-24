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
 * Url resolver pre-processor
 */
class UrlResolver implements PreProcessorInterface
{
    /**
     * @var \Magento\View\FileSystem
     */
    protected $viewFileSystem;

    /**
     * @var \Magento\Less\PreProcessor
     */
    protected $lessPreProcessor;

    /**
     * @var \Magento\Css\PreProcessor\AdapterInterface
     */
    protected $adapter;


    public function __construct()
    {
    }

    /**
     * Resolve urls
     *
     * @param string $filePath
     * @param array $params
     * @param \Magento\Filesystem\Directory\WriteInterface $targetDirectory
     * @return string
     */
    public function process($filePath, $params, $targetDirectory)
    {
    }
}
