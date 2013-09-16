<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_WebsiteRestriction
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Stub block that outputs a raw CMS-page
 *
 */
class Magento_WebsiteRestriction_Block_Cms_Stub extends Magento_Cms_Block_Page
{
    /**
     * Core registry
     *
     * @var Magento_Core_Model_Registry
     */
    protected $_coreRegistry;

    /**
     * @var Magento_Cms_Model_PageFactory
     */
    protected $_pageFactory;

    /**
     * @param Magento_Cms_Helper_Data $cmsData
     * @param Magento_Core_Block_Context $context
     * @param Magento_Core_Model_Registry $registry
     * @param Magento_Cms_Model_PageFactory $pageFactory
     * @param array $data
     */
    public function __construct(
        Magento_Cms_Helper_Data $cmsData,
        Magento_Core_Block_Context $context,
        Magento_Core_Model_Registry $registry,
        Magento_Cms_Model_PageFactory $pageFactory,
        array $data = array()
    ) {
        $this->_coreRegistry = $registry;
        $this->_pageFactory = $pageFactory;
        parent::__construct($cmsData, $context, $data);
    }

    /**
     * Retrieve page from registry if it is not there try to laod it by indetifier
     *
     * @return Magento_Cms_Model_Page
     */

    public function getPage()
    {
        if (!$this->hasData('page')) {
            $page = $this->_coreRegistry->registry('restriction_landing_page');
            if (!$page) {
                $page = $this->_pageFactory->create()->load($this->getPageIdentifier(), 'identifier');
            }
            $this->setData('page', $page);
        }
        return $this->getData('page');
    }

    protected function _prepareLayout()
    {
        $page = $this->getPage();

        if ($root = $this->getLayout()->getBlock('root')) {
            $root->addBodyClass('cms-'.$page->getIdentifier());
        }

        if ($head = $this->getLayout()->getBlock('head')) {
            $head->setTitle($page->getTitle());
            $head->setKeywords($page->getMetaKeywords());
            $head->setDescription($page->getMetaDescription());
        }
    }
}
