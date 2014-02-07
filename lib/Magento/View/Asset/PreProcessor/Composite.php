<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\View\Asset\PreProcessor;

use \Magento\View\Asset\PreProcessorFactory;

/**
 * View asset pre-processor composite
 */
class Composite implements PreProcessorInterface
{
    /**
     * @var array
     */
    protected $preProcessorsConfig = array();

    /**
     * @var PreProcessorInterface[]
     */
    protected $assetTypePreProcessors = array();

    /**
     * @var \Magento\View\Asset\PreProcessorFactory
     */
    protected $preProcessorFactory;

    /**
     * @param PreProcessorFactory $preProcessorFactory
     * @param array $preProcessorsConfig
     */
    public function __construct(
        PreProcessorFactory $preProcessorFactory,
        array $preProcessorsConfig = array()
    ) {
        $this->preProcessorFactory = $preProcessorFactory;
        $this->preProcessorsConfig = $preProcessorsConfig;
    }

    /**
     * Process view asset pro-processors
     *
     * @param string $filePath
     * @param array $params
     * @param \Magento\Filesystem\Directory\WriteInterface $targetDirectory
     * @param null|string $sourcePath
     * @return null|string
     */
    public function process($filePath, $params, $targetDirectory, $sourcePath = null)
    {
        $assetType = pathinfo($filePath, PATHINFO_EXTENSION);

        foreach ($this->getAssetTypePreProcessors($assetType) as $preProcessor) {
            $sourcePath = $preProcessor->process($filePath, $params, $targetDirectory, $sourcePath);
        }

        return $sourcePath;
    }

    /**
     * Get processors list for given asset type
     *
     * @param string $assetType
     * @return PreProcessorInterface[]
     */
    protected function getAssetTypePreProcessors($assetType)
    {
        if (!isset($this->assetTypePreProcessors[$assetType])) {
            $this->assetTypePreProcessors[$assetType] = array();
            foreach ($this->preProcessorsConfig as $preProcessorDetails) {
                if ($assetType === $preProcessorDetails['asset_type']) {
                    $this->assetTypePreProcessors[$assetType][] = $this->preProcessorFactory
                        ->create($preProcessorDetails['class']);
                }
            }
        }
        return $this->assetTypePreProcessors[$assetType];
    }
}
