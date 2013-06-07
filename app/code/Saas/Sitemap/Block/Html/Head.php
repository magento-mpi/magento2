<?php
/**
 * Saas html head block
 *
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 */
class Saas_Sitemap_Block_Html_Head extends Mage_Page_Block_Html_Head
{
    /**
     * @var Saas_Sitemap_Helper_Data
     */
    protected $_helper;

    /**
     * @param Mage_Core_Block_Template_Context $context
     * @param Magento_ObjectManager $objectManager
     * @param Mage_Core_Model_Page $page
     * @param Mage_Core_Model_Page_Asset_MergeService $assetMergeService
     * @param Mage_Core_Model_Page_Asset_MinifyService $assetMinifyService
     * @param Saas_Sitemap_Helper_Data $helper
     * @param array $data
     */
    public function __construct(
        Mage_Core_Block_Template_Context $context,
        Magento_ObjectManager $objectManager,
        Mage_Core_Model_Page $page,
        Mage_Core_Model_Page_Asset_MergeService $assetMergeService,
        Mage_Core_Model_Page_Asset_MinifyService $assetMinifyService,
        Saas_Sitemap_Helper_Data $helper,
        array $data = array()
    ) {
        parent::__construct($context, $objectManager, $page, $assetMergeService, $assetMinifyService, $data);

        $this->_helper = $helper;
    }

    /**
     * Retrieve google verification code
     *
     * @return string
     */
    public function getGoogleVerificationCode()
    {
        return $this->_helper->getGoogleVerificationCode();
    }
}
