<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Css\PreProcessor;

use Magento\View\Asset\PreProcessor\PreProcessorInterface;

/**
 * Css pre-processor composite
 */
class Composite implements PreProcessorInterface
{
    /**
     * @var PreProcessorInterface[]
     */
    protected $preProcessors = array();

    public function __construct(array $preProcessors = array())
    {
        $this->preProcessors = $this->preparePreProcessors($preProcessors);
    }

    public function process($filePath, $params, $targetDirectory)
    {
        $sourcePath = null;

        foreach ($this->preProcessors as $preProcessor) {
            $sourcePath = $preProcessor->process($filePath, $params, $targetDirectory);
        }

        return $sourcePath;
    }

    protected function preparePreProcessors($preProcessors)
    {
        $preProcessorObjects = [];
        // TBI: instantiate pre-processor classes
        return $preProcessorObjects;
    }
}
