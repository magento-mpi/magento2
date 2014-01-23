<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\View\Asset\PreProcessor;

use Magento\View\Asset;

class Composite implements PreProcessorInterface
{
    /**
     * @var PreProcessorInterface[]
     */
    protected $preProcessors = array();

    public function __construct(array $preProcessors = array())
    {
        $this->preProcessors = $preProcessors;
    }

    public function process($filePath, $params, $targetDirectory, $sourcePath = null)
    {
        $assetType = pathinfo($filePath, PATHINFO_EXTENSION);

        foreach ($this->getAssetTypePreProcessors($assetType) as $preProcessor) {
            $sourcePath = $preProcessor->process($filePath, $params, $targetDirectory, $sourcePath);
        }

        return $sourcePath;
    }

    /**
     * @param string $assetType
     * @return PreProcessorInterface[]
     */
    protected function getAssetTypePreProcessors($assetType)
    {
        $assetTypePreProcessors = array();

        // TBI: filter pre-processors according to $assetType

        return $assetTypePreProcessors ;
    }
}
