<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CustomerSegment
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Segment/customer relatio model. Model working in website scope. If website is not declared
 * all methods are working in current ran website scoupe
 *
 * @method \Magento\CustomerSegment\Model\Resource\Customer _getResource()
 * @method \Magento\CustomerSegment\Model\Resource\Customer getResource()
 * @method int getSegmentId()
 * @method \Magento\CustomerSegment\Model\Customer setSegmentId(int $value)
 * @method int getCustomerId()
 * @method \Magento\CustomerSegment\Model\Customer setCustomerId(int $value)
 * @method string getAddedDate()
 * @method \Magento\CustomerSegment\Model\Customer setAddedDate(string $value)
 * @method string getUpdatedDate()
 * @method \Magento\CustomerSegment\Model\Customer setUpdatedDate(string $value)
 * @method int getWebsiteId()
 * @method \Magento\CustomerSegment\Model\Customer setWebsiteId(int $value)
 *
 * @category    Magento
 * @package     Magento_CustomerSegment
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\CustomerSegment\Model;

class Customer extends \Magento\Core\Model\AbstractModel
{
    /**
     * @var \Magento\Core\Model\Registry
     */
    protected $_registry;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * @var int
     */
    protected $_currentWebsiteId;

    /**
     * Array of Segments collections per event name
     *
     * @var array
     */
    protected $_segmentMap = array();

    /**
     * Array of segment ids per customer id and website id
     *
     * @var array
     */
    protected $_customerWebsiteSegments = array();

    /**
     * Core event manager proxy
     *
     * @var Magento_Core_Model_Event_Manager
     */
    protected $_eventManager = null;

    /**
     * @param Magento_Core_Model_Event_Manager $eventManager
     * @param \Magento\Core\Model\Context $context
     * @param \Magento\Core\Model\Registry $registry
     * @param \Magento\Core\Model\StoreManagerInterface $storeManager
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Core\Model\Resource\AbstractResource $resource
     * @param \Magento\Data\Collection\Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        Magento_Core_Model_Event_Manager $eventManager,
        \Magento\Core\Model\Context $context,
        \Magento\Core\Model\Registry $registry,
        \Magento\Core\Model\StoreManagerInterface $storeManager,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Core\Model\Resource\AbstractResource $resource = null,
        \Magento\Data\Collection\Db $resourceCollection = null,
        array $data = array()
    ) {
        $this->_eventManager = $eventManager;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
        $this->_registry = $registry;
        $this->_customerSession = $customerSession;
        $this->_currentWebsiteId = $storeManager->getWebsite()->getId();
    }

    /**
     * Class constructor
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_init('Magento\CustomerSegment\Model\Resource\Customer');
    }

    /**
     * Get list of active segments for specific event
     *
     * @param string $eventName
     * @param int $websiteId
     * @return \Magento\CustomerSegment\Model\Resource\Segment\Collection
     */
    public function getActiveSegmentsForEvent($eventName, $websiteId)
    {
        if (!isset($this->_segmentMap[$eventName][$websiteId])) {
            $relatedSegments = \Mage::getResourceModel('Magento\CustomerSegment\Model\Resource\Segment\Collection')
                ->addEventFilter($eventName)
                ->addWebsiteFilter($websiteId)
                ->addIsActiveFilter(1);
            $this->_segmentMap[$eventName][$websiteId] = $relatedSegments;
        }
        return $this->_segmentMap[$eventName][$websiteId];
    }

    /**
     * Match all related to event segments and assign/deassign customer/visitor to segments on specific website
     *
     * @param   string $eventName
     * @param   \Magento\Customer\Model\Customer | int $customer
     * @param   \Magento\Core\Model\Website | int $website
     * @return  \Magento\CustomerSegment\Model\Customer
     */
    public function processEvent($eventName, $customer, $website)
    {
        \Magento\Profiler::start('__SEGMENTS_MATCHING__');
        $website = \Mage::app()->getWebsite($website);
        $segments = $this->getActiveSegmentsForEvent($eventName, $website->getId());

        $this->_processSegmentsValidation($customer, $website, $segments);

        \Magento\Profiler::stop('__SEGMENTS_MATCHING__');
        return $this;
    }

    /**
     * Validate all segments for specific customer/visitor on specific website
     *
     * @param   \Magento\Customer\Model\Customer $customer
     * @param   \Magento\Core\Model\Website $website
     * @return  \Magento\CustomerSegment\Model\Customer
     */
    public function processCustomer(\Magento\Customer\Model\Customer $customer, $website)
    {
        $website = \Mage::app()->getWebsite($website);
        $segments = \Mage::getResourceModel('Magento\CustomerSegment\Model\Resource\Segment\Collection')
            ->addWebsiteFilter($website)
            ->addIsActiveFilter(1);

        $this->_processSegmentsValidation($customer, $website, $segments);

        return $this;
    }

    /**
     * Check if customer is related to segments and update customer-segment relations
     *
     * @param int|null|\Magento\Customer\Model\Customer $customer
     * @param \Magento\Core\Model\Website $website
     * @param \Magento\CustomerSegment\Model\Resource\Segment\Collection $segments
     * @return \Magento\CustomerSegment\Model\Customer
     */
    protected function _processSegmentsValidation($customer, $website, $segments)
    {
        $websiteId = $website->getId();
        if ($customer instanceof \Magento\Customer\Model\Customer) {
            $customerId = $customer->getId();
        } else {
            $customerId = $customer;
        }

        $matchedIds = array();
        $notMatchedIds = array();
        $useVisitorId = !$customer || !$customerId;
        foreach ($segments as $segment) {
            if ($useVisitorId) {
                // Skip segment if it cannot be applied to visitor
                if ($segment->getApplyTo() == \Magento\CustomerSegment\Model\Segment::APPLY_TO_REGISTERED) {
                    continue;
                }
                $segment->setVisitorId(\Mage::getSingleton('Magento\Log\Model\Visitor')->getId());
            } else {
                // Skip segment if it cannot be applied to customer
                if ($segment->getApplyTo() == \Magento\CustomerSegment\Model\Segment::APPLY_TO_VISITORS) {
                    continue;
                }
            }
            $isMatched = $segment->validateCustomer($customer, $website);
            if ($isMatched) {
                $matchedIds[]   = $segment->getId();
            } else {
                $notMatchedIds[]= $segment->getId();
            }
        }


        if ($customerId) {
            $this->addCustomerToWebsiteSegments($customerId, $websiteId, $matchedIds);
            $this->removeCustomerFromWebsiteSegments($customerId, $websiteId, $notMatchedIds);
            $segmentIds = $this->_customerWebsiteSegments[$websiteId][$customerId];
        } else {
            $this->addVisitorToWebsiteSegments(\Mage::getSingleton('Magento\Customer\Model\Session'), $websiteId, $matchedIds);
            $this->removeVisitorFromWebsiteSegments(\Mage::getSingleton('Magento\Customer\Model\Session'), $websiteId, $notMatchedIds);
            $allSegments= \Mage::getSingleton('Magento\Customer\Model\Session')->getCustomerSegmentIds();
            $segmentIds = $allSegments[$websiteId];
        }

        $this->_eventManager->dispatch('magento_customersegment_ids_changed', array('segment_ids' => $segmentIds));

        return $this;
    }

    /**
     * Match customer id to all segments related to event on all websites where customer can be presented
     *
     * @param string $eventName
     * @param int $customerId
     * @return \Magento\CustomerSegment\Model\Customer
     */
    public function processCustomerEvent($eventName, $customerId)
    {
        if (\Mage::getSingleton('Magento\Customer\Model\Config\Share')->isWebsiteScope()) {
            $websiteIds = \Mage::getResourceSingleton('Magento\Customer\Model\Resource\Customer')
                ->getWebsiteId($customerId);

            if ($websiteIds) {
                $websiteIds = array($websiteIds);
            } else {
                $websiteIds = array();
            }
        } else {
            $websiteIds = \Mage::app()->getWebsites();
            $websiteIds = array_keys($websiteIds);
        }
        foreach ($websiteIds as $websiteId) {
            $this->processEvent($eventName, $customerId, $websiteId);
        }
        return $this;
    }

    /**
     * Add visitor-segment relation for specified website
     *
     * @param \Magento\Core\Model\Session\AbstractSession $visitorSession
     * @param int $websiteId
     * @param array $segmentIds
     * @return \Magento\CustomerSegment\Model\Customer
     */
    public function addVisitorToWebsiteSegments($visitorSession, $websiteId, $segmentIds)
    {
        $visitorSegmentIds = $visitorSession->getCustomerSegmentIds();
        if (!is_array($visitorSegmentIds)) {
            $visitorSegmentIds = array();
        }
        if (isset($visitorSegmentIds[$websiteId]) && is_array($visitorSegmentIds[$websiteId])) {
            $segmentsIdsForWebsite = $visitorSegmentIds[$websiteId];
            if (!empty($segmentIds)) {
                $segmentsIdsForWebsite = array_unique(array_merge($segmentsIdsForWebsite, $segmentIds));
            }
            $visitorSegmentIds[$websiteId] = $segmentsIdsForWebsite;
        } else {
            $visitorSegmentIds[$websiteId] = $segmentIds;
        }
        $visitorSession->setCustomerSegmentIds($visitorSegmentIds);
        return $this;
    }

    /**
     * Remove visitor-segment relation for specified website
     *
     * @param \Magento\Core\Model\Session\AbstractSession $visitorSession
     * @param int $websiteId
     * @param array $segmentIds
     * @return \Magento\CustomerSegment\Model\Customer
     */
    public function removeVisitorFromWebsiteSegments($visitorSession, $websiteId, $segmentIds)
    {
        $visitorCustomerSegmentIds = $visitorSession->getCustomerSegmentIds();
        if (!is_array($visitorCustomerSegmentIds)) {
            $visitorCustomerSegmentIds = array();
        }
        if (isset($visitorCustomerSegmentIds[$websiteId]) && is_array($visitorCustomerSegmentIds[$websiteId])) {
            $segmentsIdsForWebsite = $visitorCustomerSegmentIds[$websiteId];
            if (!empty($segmentIds)) {
                $segmentsIdsForWebsite = array_diff($segmentsIdsForWebsite, $segmentIds);
            }
            $visitorCustomerSegmentIds[$websiteId] = $segmentsIdsForWebsite;
        }
        $visitorSession->setCustomerSegmentIds($visitorCustomerSegmentIds);
        return $this;
    }

    /**
     * Add customer relation with segment for specific website
     *
     * @param int $customerId
     * @param int $websiteId
     * @param array $segmentIds
     * @return \Magento\CustomerSegment\Model\Customer
     */
    public function addCustomerToWebsiteSegments($customerId, $websiteId, $segmentIds)
    {
        $existingIds = $this->getCustomerSegmentIdsForWebsite($customerId, $websiteId);
        $this->_getResource()->addCustomerToWebsiteSegments($customerId, $websiteId, $segmentIds);
        $this->_customerWebsiteSegments[$websiteId][$customerId] = array_unique(array_merge($existingIds, $segmentIds));
        return $this;
    }

    /**
     * Remove customer id association with segment ids on specific website
     *
     * @param int $customerId
     * @param int $websiteId
     * @param array $segmentIds
     * @return \Magento\CustomerSegment\Model\Customer
     */
    public function removeCustomerFromWebsiteSegments($customerId, $websiteId, $segmentIds)
    {
        $existingIds = $this->getCustomerSegmentIdsForWebsite($customerId, $websiteId);
        $this->_getResource()->removeCustomerFromWebsiteSegments($customerId, $websiteId, $segmentIds);
        $this->_customerWebsiteSegments[$websiteId][$customerId] = array_diff($existingIds, $segmentIds);
        return $this;
    }

    /**
     * Get segment ids for specific customer id and website id
     *
     * @param int $customerId
     * @param int $websiteId
     * @return array
     */
    public function getCustomerSegmentIdsForWebsite($customerId, $websiteId)
    {
        if (!isset($this->_customerWebsiteSegments[$websiteId][$customerId])) {
            $this->_customerWebsiteSegments[$websiteId][$customerId] = $this->_getResource()
                ->getCustomerWebsiteSegments($customerId, $websiteId);
        }
        return $this->_customerWebsiteSegments[$websiteId][$customerId];
    }

    /**
     * Retrieve segment ids for the current customer and current website
     *
     * @return array
     */
    public function getCurrentCustomerSegmentIds()
    {
        /** @var \Magento\Customer\Model\Session $customerSession */
        $customerSession = $this->_customerSession;
        $result = array();
        /** @var \Magento\Customer\Model\Customer $customer */
        $customer = $this->_registry->registry('segment_customer');
        if (!$customer) {
            $customer = $customerSession->getCustomer();
        }
        $websiteId = $this->_currentWebsiteId;
        if (!$customer->getId()) {
            $allSegmentIds = $customerSession->getCustomerSegmentIds();
            if ((is_array($allSegmentIds) && isset($allSegmentIds[$websiteId]))) {
                $result = $allSegmentIds[$websiteId];
            }
        } else {
            $result = $this->getCustomerSegmentIdsForWebsite($customer->getId(), $this->_currentWebsiteId);
        }
        return $result;
    }
}
