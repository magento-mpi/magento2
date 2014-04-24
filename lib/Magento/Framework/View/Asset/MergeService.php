<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\View\Asset;

/**
 * Service model responsible for making a decision of whether to use the merged asset in place of original ones
 */
class MergeService
{
    /**
     * Object Manager
     *
     * @var \Magento\Framework\ObjectManager
     */
    protected $objectManager;

    /**
     * Config
     *
     * @var ConfigInterface
     */
    protected $config;

    /**
     * Filesystem
     *
     * @var \Magento\Framework\App\Filesystem
     */
    protected $filesystem;

    /**
     * State
     *
     * @var \Magento\Framework\App\State
     */
    protected $state;

    /**
     * Constructor
     *
     * @param \Magento\Framework\ObjectManager $objectManager
     * @param ConfigInterface $config
     * @param \Magento\Framework\App\Filesystem $filesystem
     * @param \Magento\Framework\App\State $state
     */
    public function __construct(
        \Magento\Framework\ObjectManager $objectManager,
        ConfigInterface $config,
        \Magento\Framework\App\Filesystem $filesystem,
        \Magento\Framework\App\State $state
    ) {
        $this->objectManager = $objectManager;
        $this->config = $config;
        $this->filesystem = $filesystem;
        $this->state = $state;
    }

    /**
     * Return merged assets, if merging is enabled for a given content type
     *
     * @param array $assets
     * @param string $contentType
     * @return array|\Iterator
     * @throws \InvalidArgumentException
     */
    public function getMergedAssets(array $assets, $contentType)
    {
        $isCss = $contentType == \Magento\Framework\View\Publisher::CONTENT_TYPE_CSS;
        $isJs = $contentType == \Magento\Framework\View\Publisher::CONTENT_TYPE_JS;
        if (!$isCss && !$isJs) {
            throw new \InvalidArgumentException("Merge for content type '{$contentType}' is not supported.");
        }

        $isCssMergeEnabled = $this->config->isMergeCssFiles();
        $isJsMergeEnabled = $this->config->isMergeJsFiles();
        if ($isCss && $isCssMergeEnabled || $isJs && $isJsMergeEnabled) {
            if ($this->state->getMode() == \Magento\Framework\App\State::MODE_PRODUCTION) {
                $mergeStrategyClass = 'Magento\Framework\View\Asset\MergeStrategy\FileExists';
            } else {
                $mergeStrategyClass = 'Magento\Framework\View\Asset\MergeStrategy\Checksum';
            }
            $mergeStrategy = $this->objectManager->get($mergeStrategyClass);

            $assets = $this->objectManager->create(
                'Magento\Framework\View\Asset\Merged',
                array('assets' => $assets, 'mergeStrategy' => $mergeStrategy)
            );
        }

        return $assets;
    }

    /**
     * Remove all merged js/css files
     *
     * @return void
     */
    public function cleanMergedJsCss()
    {
        $this->filesystem->getDirectoryWrite(
            \Magento\Framework\App\Filesystem::PUB_VIEW_CACHE_DIR
        )->delete(
            Merged::PUBLIC_MERGE_DIR
        );
    }
}
