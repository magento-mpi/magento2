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
class Magento_Core_Model_Page_Asset_MergeService
{
    /**#@+
     * XPaths where merging configuration resides
     */
    const XML_PATH_MERGE_CSS_FILES  = 'dev/css/merge_css_files';
    const XML_PATH_MERGE_JS_FILES   = 'dev/js/merge_files';
    /**#@-*/

    /**
     * @var Magento_ObjectManager
     */
    private $_objectManager;

    /**
     * @var Magento_Core_Model_Store_Config
     */
    private $_storeConfig;

    /**
     * @var Magento_Filesystem
     */
    private $_filesystem;

    /**
     * @var Magento_Core_Model_Dir
     */
    private $_dirs;

    /**
     * @var Magento_Core_Model_App_State
     */
    private $_state;

    /**
     * @param Magento_ObjectManager $objectManager
     * @param Magento_Core_Model_Store_Config $storeConfig
     * @param Magento_Filesystem $filesystem,
     * @param Magento_Core_Model_Dir $dirs
     * @param Magento_Core_Model_App_State $state
     */
    public function __construct(
        Magento_ObjectManager $objectManager,
        Magento_Core_Model_Store_Config $storeConfig,
        Magento_Filesystem $filesystem,
        Magento_Core_Model_Dir $dirs,
        Magento_Core_Model_App_State $state
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
     * @throws InvalidArgumentException
     */
    public function getMergedAssets(array $assets, $contentType)
    {
        $isCss = $contentType == Magento_Core_Model_View_Publisher::CONTENT_TYPE_CSS;
        $isJs = $contentType == Magento_Core_Model_View_Publisher::CONTENT_TYPE_JS;
        if (!$isCss && !$isJs) {
            throw new InvalidArgumentException("Merge for content type '$contentType' is not supported.");
        }

        $isCssMergeEnabled = $this->_storeConfig->getConfigFlag(self::XML_PATH_MERGE_CSS_FILES);
        $isJsMergeEnabled = $this->_storeConfig->getConfigFlag(self::XML_PATH_MERGE_JS_FILES);
        if (($isCss && $isCssMergeEnabled) || ($isJs && $isJsMergeEnabled)) {
            if ($this->_state->getMode() == Magento_Core_Model_App_State::MODE_PRODUCTION) {
                $mergeStrategyClass = 'Magento_Core_Model_Page_Asset_MergeStrategy_FileExists';
            } else {
                $mergeStrategyClass = 'Magento_Core_Model_Page_Asset_MergeStrategy_Checksum';
            }
            $mergeStrategy = $this->_objectManager->get($mergeStrategyClass);

            $assets = $this->_objectManager->create(
                'Magento_Core_Model_Page_Asset_Merged', array('assets' => $assets, 'mergeStrategy' => $mergeStrategy)
            );
        }

        return $assets;
    }

    /**
     * Remove all merged js/css files
     */
    public function cleanMergedJsCss()
    {
        $mergedDir = $this->_dirs->getDir(Magento_Core_Model_Dir::PUB_VIEW_CACHE) . '/'
            . Magento_Core_Model_Page_Asset_Merged::PUBLIC_MERGE_DIR;
        $this->_filesystem->delete($mergedDir);

        $this->_objectManager->get('Magento_Core_Helper_File_Storage_Database')
            ->deleteFolder($mergedDir);
    }
}
