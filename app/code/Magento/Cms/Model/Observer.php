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

        $pageId  = \Mage::getStoreConfig(\Magento\Cms\Helper\Page::XML_PATH_NO_COOKIES_PAGE);
        $pageUrl = \Mage::helper('Magento\Cms\Helper\Page')->getPageUrl($pageId);

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
