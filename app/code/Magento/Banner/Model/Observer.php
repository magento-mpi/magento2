<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Banner
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Banner observer model
 */
namespace Magento\Banner\Model;

class Observer
{

    /**
     * Prepare catalog rule post data to save
     *
     * @param \Magento\Event\Observer $observer
     * @return \Magento\Banner\Model\Observer
     */
    public function prepareCatalogRuleSave(\Magento\Event\Observer $observer)
    {
        $request = $observer->getEvent()->getRequest();
        $request->setPost(
            'related_banners',
            \Mage::helper('Magento\Adminhtml\Helper\Js')->decodeGridSerializedInput($request->getPost('related_banners'))
        );
        return $this;
    }

    /**
     * Bind specified banners to catalog rule
     *
     * @param   \Magento\Event\Observer $observer
     * @return  \Magento\Banner\Model\Observer
     */
    public function bindRelatedBannersToCatalogRule(\Magento\Event\Observer $observer)
    {
        $catalogRule = $observer->getEvent()->getRule();
        $resource = \Mage::getResourceModel('\Magento\Banner\Model\Resource\Banner');
        $banners = $catalogRule->getRelatedBanners();
        if (empty($banners)) {
            $banners = array();
        }
        $resource->bindBannersToCatalogRule($catalogRule->getId(), $banners);
        return $this;
    }

    /**
     * Prepare sales rule post data to save
     *
     * @param \Magento\Event\Observer $observer
     * @return \Magento\Banner\Model\Observer
     */
    public function prepareSalesRuleSave(\Magento\Event\Observer $observer)
    {
        $request = $observer->getEvent()->getRequest();
        $request->setPost(
            'related_banners',
            \Mage::helper('Magento\Adminhtml\Helper\Js')->decodeGridSerializedInput($request->getPost('related_banners'))
        );
        return $this;
    }

    /**
     * Bind specified banners to sales rule
     *
     * @param   \Magento\Event\Observer $observer
     * @return  \Magento\Banner\Model\Observer
     */
    public function bindRelatedBannersToSalesRule(\Magento\Event\Observer $observer)
    {
        $salesRule = $observer->getEvent()->getRule();
        $resource = \Mage::getResourceModel('\Magento\Banner\Model\Resource\Banner');
        $banners = $salesRule->getRelatedBanners();
        if (empty($banners)) {
            $banners = array();
        }
        $resource->bindBannersToSalesRule($salesRule->getId(), $banners);
        return $this;
    }
}
