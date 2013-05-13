<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Service model responsible for making a decision of whether to use the merged asset in place of original ones
 */
class Mage_Core_Model_Page_Asset_MergeService
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
     * @var Mage_Core_Model_Store_Config
     */
    private $_storeConfig;

    /**
     * @var Mage_Core_Model_Design_PackageInterface
     */
    private $_designPackage;

    /**
     * @param Magento_ObjectManager $objectManager
     * @param Mage_Core_Model_Store_Config $storeConfig
     * @param Mage_Core_Model_Design_PackageInterface $designPackage
     */
    public function __construct(Magento_ObjectManager $objectManager, Mage_Core_Model_Store_Config $storeConfig,
        Mage_Core_Model_Design_PackageInterface $designPackage
    ) {
        $this->_objectManager = $objectManager;
        $this->_storeConfig = $storeConfig;
        $this->_designPackage = $designPackage;
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
        $isCss = $contentType == Mage_Core_Model_Design_PackageInterface::CONTENT_TYPE_CSS;
        $isJs = $contentType == Mage_Core_Model_Design_PackageInterface::CONTENT_TYPE_JS;
        if (!$isCss && !$isJs) {
            throw new InvalidArgumentException("Merge for content type '$contentType' is not supported.");
        }

        if ($this->_designPackage->isMergingViewFilesAllowed()) {
            $isCssMergeEnabled = $this->_storeConfig->getConfigFlag(self::XML_PATH_MERGE_CSS_FILES);
            $isJsMergeEnabled = $this->_storeConfig->getConfigFlag(self::XML_PATH_MERGE_JS_FILES);
            if (($isCss && $isCssMergeEnabled) || ($isJs && $isJsMergeEnabled)) {
                $assets = $this->_objectManager->create(
                    'Mage_Core_Model_Page_Asset_Merged', array('assets' => $assets)
                );
            }
        }

        return $assets;
    }
}
