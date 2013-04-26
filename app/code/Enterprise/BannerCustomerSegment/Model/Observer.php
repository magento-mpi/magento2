<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Enterprise_BannerCustomerSegment_Model_Observer
{
    /**
     * @var Mage_Core_Model_Resource
     */
    private $_resource;

    /**
     * @var Enterprise_CustomerSegment_Model_Customer
     */
    private $_segmentCustomer;

    /**
     * @var Enterprise_CustomerSegment_Helper_Data
     */
    private $_segmentHelper;

    /**
     * @var Enterprise_CustomerSegment_Model_Resource_Segment_Collection
     */
    private $_segmentCollection;

    /**
     * @param Mage_Core_Model_Resource $resource
     * @param Enterprise_CustomerSegment_Model_Customer $segmentCustomer
     * @param Enterprise_CustomerSegment_Helper_Data $segmentHelper
     * @param Enterprise_CustomerSegment_Model_Resource_Segment_Collection $segmentCollection
     */
    public function __construct(
        Mage_Core_Model_Resource $resource,
        Enterprise_CustomerSegment_Model_Customer $segmentCustomer,
        Enterprise_CustomerSegment_Helper_Data $segmentHelper,
        Enterprise_CustomerSegment_Model_Resource_Segment_Collection $segmentCollection
    ) {
        $this->_resource = $resource;
        $this->_segmentCustomer = $segmentCustomer;
        $this->_segmentHelper = $segmentHelper;
        $this->_segmentCollection = $segmentCollection;
    }

    /**
     * Assign the list of customer segment ids associated with a banner entity, passed as an event argument
     *
     * @param Varien_Event_Observer $observer
     */
    public function loadCustomerSegmentRelations(Varien_Event_Observer $observer)
    {
        if (!$this->_segmentHelper->isEnabled()) {
            return;
        }
        /** @var Enterprise_Banner_Model_Banner $banner */
        $banner = $observer->getEvent()->getBanner();

        $adapter = $this->_resource->getConnection('read');
        $select = $adapter->select()
            ->from($this->_resource->getTableName('enterprise_banner_customersegment'))
            ->where('banner_id = ?', $banner->getId())
        ;
        $data = $adapter->fetchAll($select);
        if ($data) {
            $segmentIds = array();
            foreach ($data as $row) {
                $segmentIds[] = $row['segment_id'];
            }
            $banner->setData('customer_segment_ids', $segmentIds);
        }
    }

    /**
     * Store customer segment ids associated with a banner entity, passed as an event argument
     *
     * @param Varien_Event_Observer $observer
     */
    public function saveCustomerSegmentRelations(Varien_Event_Observer $observer)
    {
        if (!$this->_segmentHelper->isEnabled()) {
            return;
        }
        /** @var Enterprise_Banner_Model_Banner $banner */
        $banner = $observer->getEvent()->getBanner();

        $bannerId = $banner->getId();
        $segmentIds = $banner->getData('customer_segment_ids');

        $adapter = $this->_resource->getConnection('write');

        $adapter->delete(
            $this->_resource->getTableName('enterprise_banner_customersegment'),
            array('banner_id = ?' => $bannerId)
        );

        if ($segmentIds) {
            $insertRows = array();
            foreach ($segmentIds as $segmentId) {
                $insertRows[] = array('banner_id' => $bannerId, 'segment_id' => $segmentId);
            }
            $adapter->insertMultiple($this->_resource->getTableName('enterprise_banner_customersegment'), $insertRows);
        }
    }

    /**
     * Add customer segment fields to the banner form, passed as an event argument
     *
     * @param Varien_Event_Observer $observer
     */
    public function addFieldsToBannerForm(Varien_Event_Observer $observer)
    {
        if (!$this->_segmentHelper->isEnabled()) {
            return;
        }
        /* @var Varien_Data_Form $form */
        $form = $observer->getEvent()->getForm();
        /** @var Varien_Object $model */
        $model = $observer->getEvent()->getModel();
        /** @var Mage_Backend_Block_Widget_Form_Element_Dependence $afterFormBlock */
        $afterFormBlock = $observer->getEvent()->getAfterFormBlock();

        $this->_segmentHelper->addSegmentFieldsToForm($form, $model, $afterFormBlock);
    }

    /**
     * Apply customer segment filter to a collection, passed as an event argument
     *
     * @param Varien_Event_Observer $observer
     */
    public function addCustomerSegmentFilterToCollection(Varien_Event_Observer $observer)
    {
        if (!$this->_segmentHelper->isEnabled()) {
            return;
        }
        /** @var Mage_Core_Model_Resource_Db_Collection_Abstract $collection */
        $collection = $observer->getEvent()->getCollection();
        $segmentIds = $this->_segmentCustomer->getCurrentCustomerSegmentIds();
        $this->_addCustomerSegmentFilter($collection->getSelect(), $segmentIds);
    }

    /**
     * Apply customer segment filter to a select object, passed as an event argument
     *
     * @param Varien_Event_Observer $observer
     */
    public function addCustomerSegmentFilterToSelect(Varien_Event_Observer $observer)
    {
        if (!$this->_segmentHelper->isEnabled()) {
            return;
        }
        /** @var Zend_Db_Select $select */
        $select = $observer->getEvent()->getSelect();
        $segmentIds = $this->_segmentCustomer->getCurrentCustomerSegmentIds();
        $this->_addCustomerSegmentFilter($select, $segmentIds);
    }

    /**
     * Limit the scope of a select object to certain customer segments
     *
     * @param Zend_Db_Select $select
     * @param array $segmentIds
     */
    protected function _addCustomerSegmentFilter(Zend_Db_Select $select, array $segmentIds)
    {
        $select
            ->joinLeft(
                array('banner_segment' => $this->_resource->getTableName('enterprise_banner_customersegment')),
                'banner_segment.banner_id = main_table.banner_id',
                array()
            )
        ;
        if ($segmentIds) {
            $select->where('banner_segment.segment_id IS NULL OR banner_segment.segment_id IN (?)', $segmentIds);
        } else {
            $select->where('banner_segment.segment_id IS NULL');
        }
    }
}
