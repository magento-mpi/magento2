<?php
/**
 * Magento Enterprise Edition
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magento Enterprise Edition License
 * that is bundled with this package in the file LICENSE_EE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magentocommerce.com/license/enterprise-edition
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Enterprise
 * @package     Enterprise_CustomerSegment
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */
class Enterprise_CustomerSegment_Model_Customer extends Mage_Core_Model_Abstract
{
    /**
     * Array of Segments collections per event name
     *
     * @var array
     */
    protected $_segmentMap = array();

    /**
     * Array of segment ids per customer if
     *
     * @var array
     */
    protected $_customerSegments = array();

    protected function _construct()
    {
        parent::_construct();
        $this->_init('enterprise_customersegment/customer');
    }

    /**
     * Get list of active segments for specific event
     *
     * @param string $eventName
     * @param int $websiteId
     * @return Enterprise_CustomerSegment_Model_Mysql4_Segment_Collection
     */
    public function getActiveSegmentsForEvent($eventName, $websiteId)
    {
        if (!isset($this->_segmentMap[$eventName])) {
            $this->_segmentMap[$eventName] = Mage::getResourceModel('enterprise_customersegment/segment_collection')
                    ->addEventFilter($eventName)
                    ->addWebsiteFilter($websiteId)
                    ->addIsActiveFilter(1);
            Varien_Profiler::start('__SEGMENTS_MATCHING_AFTERLOAD__');
            $this->_segmentMap[$eventName]->walk('afterLoad');
            Varien_Profiler::stop('__SEGMENTS_MATCHING_AFTERLOAD__');
        }
        return $this->_segmentMap[$eventName];
    }

    /**
     * Match all related to event segments and assign/deassign customer to segments
     *
     * @param string $eventName
     * @param Mage_Customer_Model_Customer $customer
     * @param null | int $website
     * @return Enterprise_CustomerSegment_Model_Customer
     */
    public function processEvent($eventName, $customer, $website)
    {
        Varien_Profiler::start('__SEGMENTS_MATCHING__');
        $segments = $this->getActiveSegmentsForEvent($eventName, $website);
        $matchedIds     = array();
        $notMatchedIds  = array();
        foreach ($segments as $segment) {
            $isMatched = $segment->validateCustomer($customer, $website);
            if ($isMatched) {
                $matchedIds[]   = $segment->getId();
            } else {
                $notMatchedIds[]= $segment->getId();
            }
        }


        Varien_Profiler::stop('__SEGMENTS_MATCHING__');
        return $this;
    }

    /**
     * Assign customer with specific segment ids
     *
     * @param Mage_Customer_Model_Customer $customer
     * @param array $segmentIds
     * @return Enterprise_CustomerSegment_Model_Customer
     */
    public function addCustomerToSegments($customer, $segmentIds)
    {
        $customerId = $customer->getId();
        $existingIds = $this->getCustomerSegmentIds($customer);
        $this->_getResource()->addCustomerToSegments($customerId, $segmentIds);
        $this->_customerSegments[$customerId] = array_merge($existingIds, $segmentIds);
        return $this;
    }

    /**
     * Unassign customer from specific segment ids
     *
     * @param Mage_Customer_Model_Customer $customer
     * @param array $segmentIds
     * @return Enterprise_CustomerSegment_Model_Customer
     */
    public function removeCustomerFromSegments($customer, $segmentIds)
    {
        $customerId = $customer->getId();
        $existingIds = $this->getCustomerSegmentIds($customer);
        $this->_getResource()->removeCustomerFromSegments($customerId, $segmentIds);
        $this->_customerSegments[$customerId] = array_diff($existingIds, $segmentIds);
        return $this;
    }

    /**
     * Get array of segment ids for customer
     *
     * @param Mage_Customer_Model_Customer $customer
     * @return array
     */
    public function getCustomerSegmentIds(Mage_Customer_Model_Customer $customer)
    {
        $customerId = $customer->getId();
        if (!isset($this->_customerSegments[$customerId])) {
            $this->_customerSegments[$customerId] = $this->_getResource()->getCustomerSegments($customerId);
        }
        return $this->_customerSegments[$customerId];
    }
}
