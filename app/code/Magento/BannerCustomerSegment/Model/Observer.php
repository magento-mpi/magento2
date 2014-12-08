<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\BannerCustomerSegment\Model;

class Observer
{
    /**
     * @var \Magento\CustomerSegment\Model\Customer
     */
    private $_segmentCustomer;

    /**
     * @var \Magento\CustomerSegment\Helper\Data
     */
    private $_segmentHelper;

    /**
     * @var \Magento\CustomerSegment\Model\Resource\Segment\Collection
     */
    private $_segmentCollection;

    /**
     * @var \Magento\BannerCustomerSegment\Model\Resource\BannerSegmentLink
     */
    private $_bannerSegmentLink;

    /**
     * @param \Magento\CustomerSegment\Model\Customer $segmentCustomer
     * @param \Magento\CustomerSegment\Helper\Data $segmentHelper
     * @param \Magento\CustomerSegment\Model\Resource\Segment\Collection $segmentCollection
     * @param \Magento\BannerCustomerSegment\Model\Resource\BannerSegmentLink $bannerSegmentLink
     */
    public function __construct(
        \Magento\CustomerSegment\Model\Customer $segmentCustomer,
        \Magento\CustomerSegment\Helper\Data $segmentHelper,
        \Magento\CustomerSegment\Model\Resource\Segment\Collection $segmentCollection,
        \Magento\BannerCustomerSegment\Model\Resource\BannerSegmentLink $bannerSegmentLink
    ) {
        $this->_segmentCustomer = $segmentCustomer;
        $this->_segmentHelper = $segmentHelper;
        $this->_segmentCollection = $segmentCollection;
        $this->_bannerSegmentLink = $bannerSegmentLink;
    }

    /**
     * Assign the list of customer segment ids associated with a banner entity, passed as an event argument
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function loadCustomerSegmentRelations(\Magento\Framework\Event\Observer $observer)
    {
        if (!$this->_segmentHelper->isEnabled()) {
            return;
        }
        /** @var \Magento\Banner\Model\Banner $banner */
        $banner = $observer->getEvent()->getBanner();
        $segmentIds = $this->_bannerSegmentLink->loadBannerSegments($banner->getId());
        $banner->setData('customer_segment_ids', $segmentIds);
    }

    /**
     * Store customer segment ids associated with a banner entity, passed as an event argument
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     * @throws \UnexpectedValueException
     */
    public function saveCustomerSegmentRelations(\Magento\Framework\Event\Observer $observer)
    {
        if (!$this->_segmentHelper->isEnabled()) {
            return;
        }
        /** @var \Magento\Banner\Model\Banner $banner */
        $banner = $observer->getEvent()->getBanner();
        $segmentIds = $banner->getData('customer_segment_ids') ?: [];
        if (!is_array($segmentIds)) {
            throw new \UnexpectedValueException(
                'Customer segments associated with a banner are expected to be defined as an array of identifiers.'
            );
        }
        $segmentIds = array_map('intval', $segmentIds);
        $this->_bannerSegmentLink->saveBannerSegments($banner->getId(), $segmentIds);
    }

    /**
     * Add customer segment fields to the banner form, passed as an event argument
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function addFieldsToBannerForm(\Magento\Framework\Event\Observer $observer)
    {
        if (!$this->_segmentHelper->isEnabled()) {
            return;
        }
        /* @var \Magento\Framework\Data\Form $form */
        $form = $observer->getEvent()->getForm();
        /** @var \Magento\Framework\Object $model */
        $model = $observer->getEvent()->getModel();
        /** @var \Magento\Backend\Block\Widget\Form\Element\Dependence $afterFormBlock */
        $afterFormBlock = $observer->getEvent()->getAfterFormBlock();
        $this->_segmentHelper->addSegmentFieldsToForm($form, $model, $afterFormBlock);
    }

    /**
     * Apply customer segment filter to a collection, passed as an event argument
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function addCustomerSegmentFilterToCollection(\Magento\Framework\Event\Observer $observer)
    {
        if (!$this->_segmentHelper->isEnabled()) {
            return;
        }
        /** @var \Magento\Framework\Model\Resource\Db\Collection\AbstractCollection $collection */
        $collection = $observer->getEvent()->getCollection();
        $segmentIds = $this->_segmentCustomer->getCurrentCustomerSegmentIds();
        $this->_bannerSegmentLink->addBannerSegmentFilter($collection->getSelect(), $segmentIds);
    }

    /**
     * Apply customer segment filter to a select object, passed as an event argument
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function addCustomerSegmentFilterToSelect(\Magento\Framework\Event\Observer $observer)
    {
        if (!$this->_segmentHelper->isEnabled()) {
            return;
        }
        /** @var \Zend_Db_Select $select */
        $select = $observer->getEvent()->getSelect();
        $segmentIds = $this->_segmentCustomer->getCurrentCustomerSegmentIds();
        $this->_bannerSegmentLink->addBannerSegmentFilter($select, $segmentIds);
    }
}
