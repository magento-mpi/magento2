<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Service model responsible for making a decision of whether to use the merged asset in place of original ones
 */
namespace Magento\Core\Model\Page\Asset;

class MergeService
{
    /**#@+
     * XPaths where merging configuration resides
     */
    const XML_PATH_MERGE_CSS_FILES  = 'dev/css/merge_css_files';
    const XML_PATH_MERGE_JS_FILES   = 'dev/js/merge_files';
    /**#@-*/

    /**
     * @var \Magento\ObjectManager
     */
    private $_objectManager;

    /**
     * @var \Magento\Core\Model\Store\Config
     */
    private $_storeConfig;

    /**
     * @var \Magento\Filesystem
     */
    private $_filesystem;

    /**
     * @var \Magento\App\Dir
     */
    private $_dirs;

    /**
     * @var \Magento\App\State
     */
    private $_state;

    /**
     * @param \Magento\ObjectManager $objectManager
     * @param \Magento\Core\Model\Store\Config $storeConfig
     * @param \Magento\Filesystem $filesystem,
     * @param \Magento\App\Dir $dirs
     * @param \Magento\App\State $state
     */
    public function __construct(
        \Magento\ObjectManager $objectManager,
        \Magento\Core\Model\Store\Config $storeConfig,
        \Magento\Filesystem $filesystem,
        \Magento\App\Dir $dirs,
        \Magento\App\State $state
    ) {
        $this->_objectManager = $objectManager;
        $this->_storeConfig = $storeConfig;
        $this->_filesystem = $filesystem;
        $this->_dirs = $dirs;
        $this->_state = $state;
    }

    /**
     * Return merged assets, if merging is enabled for a given content type
     *
     * @param array $assets
     * @param string $contentType
     * @return array|Iterator
     * @throws \InvalidArgumentException
     */
    public function getMergedAssets(array $assets, $contentType)
    {
        $isCss = $contentType == \Magento\View\Publisher::CONTENT_TYPE_CSS;
        $isJs = $contentType == \Magento\View\Publisher::CONTENT_TYPE_JS;
        if (!$isCss && !$isJs) {
            throw new \InvalidArgumentException("Merge for content type '$contentType' is not supported.");
        }

        $isCssMergeEnabled = $this->_storeConfig->getConfigFlag(self::XML_PATH_MERGE_CSS_FILES);
        $isJsMergeEnabled = $this->_storeConfig->getConfigFlag(self::XML_PATH_MERGE_JS_FILES);
        if (($isCss && $isCssMergeEnabled) || ($isJs && $isJsMergeEnabled)) {
            if ($this->_state->getMode() == \Magento\App\State::MODE_PRODUCTION) {
                $mergeStrategyClass = 'Magento\Core\Model\Page\Asset\MergeStrategy\FileExists';
            } else {
                $mergeStrategyClass = 'Magento\Core\Model\Page\Asset\MergeStrategy\Checksum';
            }
            $mergeStrategy = $this->_objectManager->get($mergeStrategyClass);

            $assets = $this->_objectManager->create(
                'Magento\Core\Model\Page\Asset\Merged', array('assets' => $assets, 'mergeStrategy' => $mergeStrategy)
            );
        }

        return $assets;
    }

    /**
     * Remove all merged js/css files
     */
    public function cleanMergedJsCss()
    {
        $cacheDir = $this->_filesystem->getDirectoryWrite(\Magento\Filesystem\DirectoryList::PUB_VIEW_CACHE);
        $cacheDir->delete($cacheDir->getRelativePath(\Magento\Core\Model\Page\Asset\Merged::PUBLIC_MERGE_DIR));
        $this->_objectManager->get('Magento\Core\Helper\File\Storage\Database')
            ->deleteFolder($cacheDir->getAbsolutePath(\Magento\Core\Model\Page\Asset\Merged::PUBLIC_MERGE_DIR));
    }
}
