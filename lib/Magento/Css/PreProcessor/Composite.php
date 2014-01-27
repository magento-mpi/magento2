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
     * @var PreProcessorInterface[]
     */
    protected $preProcessors = array();

    /**
     * @var \Magento\View\Asset\PreProcessorFactory
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
        $this->preProcessors = $this->preparePreProcessors($preProcessors);
    }

    public function process($filePath, $params, $targetDirectory, $sourcePath = null)
    {
        foreach ($this->preProcessors as $preProcessor) {
            $sourcePath = $preProcessor->process($filePath, $params, $targetDirectory, $sourcePath);
        }

        return $sourcePath;
    }

    /**
     * @param array $preProcessors
     * @return \Magento\View\Asset\PreProcessor\PreProcessorInterface[]
     */
    protected function preparePreProcessors($preProcessors)
    {
        if (empty($this->preProcessors)) {
            foreach ($preProcessors as $preProcessorClass) {
                $this->preProcessors[] = $this->preProcessorFactory->create($preProcessorClass);
            }
        }
        return $this->preProcessors;
    }
}
