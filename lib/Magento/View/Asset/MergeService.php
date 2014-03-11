<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\View\Asset;

/**
 * Service model responsible for making a decision of whether to use the merged asset in place of original ones
 */
class MergeService
{
    /**
     * Object Manager
     *
     * @var \Magento\ObjectManager
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
     * @var \Magento\App\Filesystem
     */
    protected $filesystem;

    /**
     * State
     *
     * @var \Magento\App\State
     */
    protected $state;

    /**
     * Constructor
     *
     * @param \Magento\ObjectManager $objectManager
     * @param ConfigInterface $config
     * @param \Magento\App\Filesystem $filesystem
     * @param \Magento\App\State $state
     */
    public function __construct(
        \Magento\ObjectManager $objectManager,
        ConfigInterface $config,
        \Magento\App\Filesystem $filesystem,
        \Magento\App\State $state
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
        $isCss = $contentType == \Magento\View\Publisher::CONTENT_TYPE_CSS;
        $isJs = $contentType == \Magento\View\Publisher::CONTENT_TYPE_JS;
        if (!$isCss && !$isJs) {
            throw new \InvalidArgumentException("Merge for content type '$contentType' is not supported.");
        }

        $isCssMergeEnabled = $this->config->isMergeCssFiles();
        $isJsMergeEnabled = $this->config->isMergeJsFiles();
        if (($isCss && $isCssMergeEnabled) || ($isJs && $isJsMergeEnabled)) {
            if ($this->state->getMode() == \Magento\App\State::MODE_PRODUCTION) {
                $mergeStrategyClass = 'Magento\View\Asset\MergeStrategy\FileExists';
            } else {
                $mergeStrategyClass = 'Magento\View\Asset\MergeStrategy\Checksum';
            }
            $mergeStrategy = $this->objectManager->get($mergeStrategyClass);

            $assets = $this->objectManager->create(
                'Magento\View\Asset\Merged', array('assets' => $assets, 'mergeStrategy' => $mergeStrategy)
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
        $this->filesystem->getDirectoryWrite(\Magento\App\Filesystem::PUB_VIEW_CACHE_DIR)
            ->delete(Merged::PUBLIC_MERGE_DIR);
    }
}
