<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Css\PreProcessor;

use Magento\View\Asset\PreProcessor\PreProcessorInterface;
use \Magento\View\Asset\PreProcessorFactory;

/**
 * Css pre-processor composite
 */
class Composite implements PreProcessorInterface
{
    /**
     * Temporary directory prefix
     */
    const TMP_VIEW_DIR   = 'view';

    /**
     * @var PreProcessorInterface[]
     */
    protected $preProcessors = array();

    /**
     * @var PreProcessorFactory
     */
    protected $preProcessorFactory;

    /**
     * @param PreProcessorFactory $preProcessorFactory
     * @param array $preProcessors
     */
    public function __construct(
        PreProcessorFactory $preProcessorFactory,
        array $preProcessors = array()
    ) {
        $this->preProcessorFactory = $preProcessorFactory;
        $this->preparePreProcessors($preProcessors);
    }

    /**
     * @param \Magento\View\Publisher\FileInterface $publisherFile
     * @param \Magento\Filesystem\Directory\WriteInterface $targetDirectory
     * @return \Magento\View\Publisher\FileInterface
     */
    public function process(\Magento\View\Publisher\FileInterface $publisherFile, $targetDirectory)
    {
        foreach ($this->preProcessors as $preProcessor) {
            $publisherFile = $preProcessor->process($publisherFile, $targetDirectory);
        }

        return $publisherFile;
    }

    /**
     * @param array $preProcessors
     * @return PreProcessorInterface[]
     */
    protected function preparePreProcessors($preProcessors)
    {
        if (empty($this->preProcessors)) {
            foreach ($preProcessors as $preProcessorClass) {
                $this->preProcessors[] = $this->preProcessorFactory->create($preProcessorClass);
            }
        }
        return $this;
    }
}
