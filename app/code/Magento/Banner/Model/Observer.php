<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

/**
 * Banner observer model
 */
namespace Magento\Banner\Model;

class Observer
{
    /**
     * Adminhtml js
     *
     * @var \Magento\Backend\Helper\Js
     */
    protected $_adminhtmlJs = null;

    /**
     * Banner factory
     *
     * @var \Magento\Banner\Model\Resource\BannerFactory
     */
    protected $_bannerFactory = null;

    /**
     * @param \Magento\Backend\Helper\Js $adminhtmlJs
     * @param \Magento\Banner\Model\Resource\BannerFactory $bannerFactory
     */
    public function __construct(
        \Magento\Backend\Helper\Js $adminhtmlJs,
        \Magento\Banner\Model\Resource\BannerFactory $bannerFactory
    ) {
        $this->_adminhtmlJs = $adminhtmlJs;
        $this->_bannerFactory = $bannerFactory;
    }

    /**
     * Prepare catalog rule post data to save
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return \Magento\Banner\Model\Observer
     */
    public function prepareCatalogRuleSave(\Magento\Framework\Event\Observer $observer)
    {
        $request = $observer->getEvent()->getRequest();
        $request->setPost(
            'related_banners',
            $this->_adminhtmlJs->decodeGridSerializedInput($request->getPost('related_banners'))
        );
        return $this;
    }

    /**
     * Bind specified banners to catalog rule
     *
     * @param   \Magento\Framework\Event\Observer $observer
     * @return  \Magento\Banner\Model\Observer
     */
    public function bindRelatedBannersToCatalogRule(\Magento\Framework\Event\Observer $observer)
    {
        $catalogRule = $observer->getEvent()->getRule();
        $banners = $catalogRule->getRelatedBanners();
        if (empty($banners)) {
            $banners = [];
        }
        $this->_bannerFactory->create()->bindBannersToCatalogRule($catalogRule->getId(), $banners);
        return $this;
    }

    /**
     * Prepare sales rule post data to save
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return \Magento\Banner\Model\Observer
     */
    public function prepareSalesRuleSave(\Magento\Framework\Event\Observer $observer)
    {
        $request = $observer->getEvent()->getRequest();
        $request->setPost(
            'related_banners',
            $this->_adminhtmlJs->decodeGridSerializedInput($request->getPost('related_banners'))
        );
        return $this;
    }

    /**
     * Bind specified banners to sales rule
     *
     * @param   \Magento\Framework\Event\Observer $observer
     * @return  \Magento\Banner\Model\Observer
     */
    public function bindRelatedBannersToSalesRule(\Magento\Framework\Event\Observer $observer)
    {
        $salesRule = $observer->getEvent()->getRule();
        $banners = $salesRule->getRelatedBanners();
        if (empty($banners)) {
            $banners = [];
        }
        $this->_bannerFactory->create()->bindBannersToSalesRule($salesRule->getId(), $banners);
        return $this;
    }
}
