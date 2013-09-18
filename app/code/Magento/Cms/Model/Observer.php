<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Cms
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * CMS Observer model
 *
 * @category   Magento
 * @package    Magento_Cms
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Cms_Model_Observer
{
    /**
     * Cms page
     *
     * @var Magento_Cms_Helper_Page
     */
    protected $_cmsPage = null;

    /**
     * Core store config
     *
     * @var Magento_Core_Model_Store_Config
     */
    protected $_coreStoreConfig;

    /**
     * @param Magento_Cms_Helper_Page $cmsPage
     * @param Magento_Core_Model_Store_Config $coreStoreConfig
     */
    public function __construct(
        Magento_Cms_Helper_Page $cmsPage,
        Magento_Core_Model_Store_Config $coreStoreConfig
    ) {
        $this->_cmsPage = $cmsPage;
        $this->_coreStoreConfig = $coreStoreConfig;
    }

    /**
     * Modify No Route Forward object
     *
     * @param Magento_Event_Observer $observer
     * @return Magento_Cms_Model_Observer
     */
    public function noRoute(Magento_Event_Observer $observer)
    {
        $observer->getEvent()->getStatus()
            ->setLoaded(true)
            ->setForwardModule('cms')
            ->setForwardController('index')
            ->setForwardAction('noRoute');
        return $this;
    }

    /**
     * Modify no Cookies forward object
     *
     * @param Magento_Event_Observer $observer
     * @return Magento_Cms_Model_Observer
     */
    public function noCookies(Magento_Event_Observer $observer)
    {
        $redirect = $observer->getEvent()->getRedirect();

        $pageId  = $this->_coreStoreConfig->getConfig(Magento_Cms_Helper_Page::XML_PATH_NO_COOKIES_PAGE);
        $pageUrl = $this->_cmsPage->getPageUrl($pageId);

        if ($pageUrl) {
            $redirect->setRedirectUrl($pageUrl);
        }
        else {
            $redirect->setRedirect(true)
                ->setPath('cms/index/noCookies')
                ->setArguments(array());
        }
        return $this;
    }

}
