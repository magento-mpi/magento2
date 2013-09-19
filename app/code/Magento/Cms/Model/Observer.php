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
namespace Magento\Cms\Model;

class Observer
{
    /**
     * Cms page
     *
     * @var \Magento\Cms\Helper\Page
     */
    protected $_cmsPage = null;

    /**
     * Core store config
     *
     * @var \Magento\Core\Model\Store\Config
     */
    protected $_coreStoreConfig;

    /**
     * @param \Magento\Cms\Helper\Page $cmsPage
     * @param \Magento\Core\Model\Store\Config $coreStoreConfig
     */
    public function __construct(
        \Magento\Cms\Helper\Page $cmsPage,
        \Magento\Core\Model\Store\Config $coreStoreConfig
    ) {
        $this->_cmsPage = $cmsPage;
        $this->_coreStoreConfig = $coreStoreConfig;
    }

    /**
     * Modify No Route Forward object
     *
     * @param \Magento\Event\Observer $observer
     * @return \Magento\Cms\Model\Observer
     */
    public function noRoute(\Magento\Event\Observer $observer)
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
     * @param \Magento\Event\Observer $observer
     * @return \Magento\Cms\Model\Observer
     */
    public function noCookies(\Magento\Event\Observer $observer)
    {
        $redirect = $observer->getEvent()->getRedirect();

        $pageId  = $this->_coreStoreConfig->getConfig(\Magento\Cms\Helper\Page::XML_PATH_NO_COOKIES_PAGE);
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
