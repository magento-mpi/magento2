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
     * Adminhtml js
     *
     * @var \Magento\Adminhtml\Helper\Js
     */
    protected $_adminhtmlJs = null;

    /**
     * Banner factory
     *
     * @var \Magento\Banner\Model\Resource\BannerFactory
     */
    protected $_bannerFactory = null;

    /**
     * @param \Magento\Adminhtml\Helper\Js $adminhtmlJs
     * @param \Magento\Banner\Model\Resource\BannerFactory $bannerFactory
     */
    public function __construct(
        \Magento\Adminhtml\Helper\Js $adminhtmlJs,
        \Magento\Banner\Model\Resource\BannerFactory $bannerFactory
    ) {
        $this->_adminhtmlJs = $adminhtmlJs;
        $this->_bannerFactory = $bannerFactory;
    }

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
            $this->_adminhtmlJs->decodeGridSerializedInput($request->getPost('related_banners'))
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
        $banners = $catalogRule->getRelatedBanners();
        if (empty($banners)) {
            $banners = array();
        }
        $this->_bannerFactory->create()
            ->bindBannersToCatalogRule($catalogRule->getId(), $banners);
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
            $this->_adminhtmlJs->decodeGridSerializedInput($request->getPost('related_banners'))
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
        $banners = $salesRule->getRelatedBanners();
        if (empty($banners)) {
            $banners = array();
        }
        $this->_bannerFactory->create()
            ->bindBannersToSalesRule($salesRule->getId(), $banners);
        return $this;
    }
}
