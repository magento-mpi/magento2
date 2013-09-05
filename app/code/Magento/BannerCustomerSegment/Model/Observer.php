<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_BannerCustomerSegment_Model_Observer
{
    /**
     * @var Magento_CustomerSegment_Model_Customer
     */
    private $_segmentCustomer;

    /**
     * @var Magento_CustomerSegment_Helper_Data
     */
    private $_segmentHelper;

    /**
     * @var Magento_CustomerSegment_Model_Resource_Segment_Collection
     */
    private $_segmentCollection;

    /**
     * @var Magento_BannerCustomerSegment_Model_Resource_BannerSegmentLink
     */
    private $_bannerSegmentLink;

    /**
     * @param Magento_CustomerSegment_Model_Customer $segmentCustomer
     * @param Magento_CustomerSegment_Helper_Data $segmentHelper
     * @param Magento_CustomerSegment_Model_Resource_Segment_Collection $segmentCollection
     * @param Magento_BannerCustomerSegment_Model_Resource_BannerSegmentLink $bannerSegmentLink
     */
    public function __construct(
        Magento_CustomerSegment_Model_Customer $segmentCustomer,
        Magento_CustomerSegment_Helper_Data $segmentHelper,
        Magento_CustomerSegment_Model_Resource_Segment_Collection $segmentCollection,
        Magento_BannerCustomerSegment_Model_Resource_BannerSegmentLink $bannerSegmentLink
    ) {
        $this->_segmentCustomer = $segmentCustomer;
        $this->_segmentHelper = $segmentHelper;
        $this->_segmentCollection = $segmentCollection;
        $this->_bannerSegmentLink = $bannerSegmentLink;
    }

    /**
     * Assign the list of customer segment ids associated with a banner entity, passed as an event argument
     *
     * @param \Magento\Event\Observer $observer
     */
    public function loadCustomerSegmentRelations(\Magento\Event\Observer $observer)
    {
        if (!$this->_segmentHelper->isEnabled()) {
            return;
        }
        /** @var Magento_Banner_Model_Banner $banner */
        $banner = $observer->getEvent()->getBanner();
        $segmentIds = $this->_bannerSegmentLink->loadBannerSegments($banner->getId());
        $banner->setData('customer_segment_ids', $segmentIds);
    }

    /**
     * Store customer segment ids associated with a banner entity, passed as an event argument
     *
     * @param \Magento\Event\Observer $observer
     * @throws UnexpectedValueException
     */
    public function saveCustomerSegmentRelations(\Magento\Event\Observer $observer)
    {
        if (!$this->_segmentHelper->isEnabled()) {
            return;
        }
        /** @var Magento_Banner_Model_Banner $banner */
        $banner = $observer->getEvent()->getBanner();
        $segmentIds = $banner->getData('customer_segment_ids') ?: array();
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
     * @param \Magento\Event\Observer $observer
     */
    public function addFieldsToBannerForm(\Magento\Event\Observer $observer)
    {
        if (!$this->_segmentHelper->isEnabled()) {
            return;
        }
        /* @var \Magento\Data\Form $form */
        $form = $observer->getEvent()->getForm();
        /** @var \Magento\Object $model */
        $model = $observer->getEvent()->getModel();
        /** @var Magento_Backend_Block_Widget_Form_Element_Dependence $afterFormBlock */
        $afterFormBlock = $observer->getEvent()->getAfterFormBlock();
        $this->_segmentHelper->addSegmentFieldsToForm($form, $model, $afterFormBlock);
    }

    /**
     * Apply customer segment filter to a collection, passed as an event argument
     *
     * @param \Magento\Event\Observer $observer
     */
    public function addCustomerSegmentFilterToCollection(\Magento\Event\Observer $observer)
    {
        if (!$this->_segmentHelper->isEnabled()) {
            return;
        }
        /** @var Magento_Core_Model_Resource_Db_Collection_Abstract $collection */
        $collection = $observer->getEvent()->getCollection();
        $segmentIds = $this->_segmentCustomer->getCurrentCustomerSegmentIds();
        $this->_bannerSegmentLink->addBannerSegmentFilter($collection->getSelect(), $segmentIds);
    }

    /**
     * Apply customer segment filter to a select object, passed as an event argument
     *
     * @param \Magento\Event\Observer $observer
     */
    public function addCustomerSegmentFilterToSelect(\Magento\Event\Observer $observer)
    {
        if (!$this->_segmentHelper->isEnabled()) {
            return;
        }
        /** @var Zend_Db_Select $select */
        $select = $observer->getEvent()->getSelect();
        $segmentIds = $this->_segmentCustomer->getCurrentCustomerSegmentIds();
        $this->_bannerSegmentLink->addBannerSegmentFilter($select, $segmentIds);
    }
}
