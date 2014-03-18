<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Css\PreProcessor;

use Magento\View\Asset\PreProcessorInterface;
use Magento\View\Asset\LocalInterface;

class Less implements PreProcessorInterface
{
    /**
     * @var \Magento\Less\FileGenerator
     */
    protected $fileGenerator;

    /**
     * @var AdapterInterface
     */
    protected $adapter;

    /**
     * @var \Magento\Logger
     */
    protected $logger;

    /**
     * @param \Magento\Less\FileGenerator $fileGenerator
     * @param AdapterInterface $adapter
     * @param \Magento\Logger $logger
     */
    public function __construct(
        \Magento\Less\FileGenerator $fileGenerator,
        AdapterInterface $adapter,
        \Magento\Logger $logger
    ) {
        $this->fileGenerator = $fileGenerator;
        $this->adapter = $adapter;
        $this->logger = $logger;
    }

    /**
     * {@inheritdoc}
     */
    public function process($content, $contentType, LocalInterface $asset)
    {
        try {
            $tmpLessFile = $this->fileGenerator->generateLessFileTree($content, $asset);
            $content = $this->adapter->process($tmpLessFile);
            $contentType = 'css';
        } catch (\Magento\Filesystem\FilesystemException $e) {
            $this->logger->logException($e);
        } catch (Adapter\AdapterException $e) {
            $this->logger->logException($e);
        }
        return array($content, $contentType);
    }
}
