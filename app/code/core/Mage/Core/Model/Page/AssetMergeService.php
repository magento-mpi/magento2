<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Core_Model_Page_AssetMergeService
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
     * @var Mage_Core_Model_Store
     */
    private $_store;

    /**
     * @param Magento_ObjectManager $objectManager
     * @param Mage_Core_Model_StoreManager $storeManager
     */
    public function __construct(Magento_ObjectManager $objectManager, Mage_Core_Model_StoreManager $storeManager)
    {
        $this->_objectManager = $objectManager;
        $this->_store = $storeManager->getStore();
    }

    /**
     * Retrieve boolean value of a store config flag
     *
     * @param string $path
     * @return bool
     */
    private function _getConfigFlag($path)
    {
        $result = $this->_store->getConfig($path);
        return (!empty($result) && 'false' !== $result);
    }

    /**
     * Return merged assets, if merging is enabled for a given content type
     *
     * @param array $assets
     * @param string $contentType
     * @return array
     * @throws InvalidArgumentException
     */
    public function getMergedAssets(array $assets, $contentType)
    {
        $isCss = $contentType == Mage_Core_Model_Design_Package::CONTENT_TYPE_CSS;
        $isJs = $contentType == Mage_Core_Model_Design_Package::CONTENT_TYPE_JS;
        if (!$isCss && !$isJs) {
            throw new InvalidArgumentException("Merge for content type '$contentType' is not supported.");
        }
        $isCssMergeEnabled = $this->_getConfigFlag(self::XML_PATH_MERGE_CSS_FILES);
        $isJsMergeEnabled = $this->_getConfigFlag(self::XML_PATH_MERGE_JS_FILES);
        if (($isCss && $isCssMergeEnabled) || ($isJs && $isJsMergeEnabled)) {
            $assets = array(
                $this->_objectManager->create('Mage_Core_Model_Asset_Merged', array('assets' => $assets), false)
            );
        }
        return $assets;
    }
}
