<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\View\Asset\PreProcessor;

use Magento\Framework\View\Asset\PreProcessorFactory;

/**
 * View asset pre-processor composite
 */
class Composite implements PreProcessorInterface
{
    /**
     * Pre-processor config
     *
     * @var array
     */
    protected $preProcessorsConfig = array();

    /**
     * Asset type pre-processor
     *
     * @var PreProcessorInterface[]
     */
    protected $assetTypePreProcessors = array();

    /**
     * Pre-processor factory
     *
     * @var \Magento\Framework\View\Asset\PreProcessorFactory
     */
    protected $preProcessorFactory;

    /**
     * Constructor
     *
     * @param PreProcessorFactory $preProcessorFactory
     * @param array $preProcessorsConfig
     */
    public function __construct(PreProcessorFactory $preProcessorFactory, array $preProcessorsConfig = array())
    {
        $this->preProcessorFactory = $preProcessorFactory;
        $this->preProcessorsConfig = $preProcessorsConfig;
    }

    /**
     * Process view asset pro-processors
     *
     * @param \Magento\Framework\View\Publisher\FileInterface $publisherFile
     * @param \Magento\Framework\Filesystem\Directory\WriteInterface $targetDirectory
     * @return \Magento\Framework\View\Publisher\FileInterface
     */
    public function process(\Magento\Framework\View\Publisher\FileInterface $publisherFile, $targetDirectory)
    {
        foreach ($this->getAssetTypePreProcessors($publisherFile->getExtension()) as $preProcessor) {
            $publisherFile = $preProcessor->process($publisherFile, $targetDirectory);
        }

        return $publisherFile;
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
                    $this->assetTypePreProcessors[$assetType][] = $this->preProcessorFactory->create(
                        $preProcessorDetails['class']
                    );
                }
            }
        }
        return $this->assetTypePreProcessors[$assetType];
    }
}
