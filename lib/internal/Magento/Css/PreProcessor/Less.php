<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Css\PreProcessor;

use Magento\View\Asset\PreProcessorInterface;

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
    public function process(\Magento\View\Asset\PreProcessor\Chain $chain)
    {
        $contentType = $chain->getContentType();
        try {
            $chain->setContentType('less');
            $tmpLessFile = $this->fileGenerator->generateLessFileTree($chain);
            $chain->setContent($this->adapter->process($tmpLessFile));
            $chain->setContentType('css');
        } catch (\Magento\Filesystem\FilesystemException $e) {
            $chain->setContentType($contentType);
            $this->logger->logException($e);
        } catch (Adapter\AdapterException $e) {
            $chain->setContentType($contentType);
            $this->logger->logException($e);
        }
    }
}
