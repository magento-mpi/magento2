<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CustomerSegment
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CustomerSegment\Model;

use \Magento\CustomerSegment\Helper\Data;

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
 */
class Customer extends \Magento\Framework\Model\AbstractModel
{
    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

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
     * @var \Magento\Log\Model\Visitor
     */
    protected $_visitor;

    /**
     * @var \Magento\Customer\Model\Config\Share
     */
    protected $_configShare;

    /**
     * @var \Magento\Customer\Model\Resource\Customer
     */
    protected $_resourceCustomer;

    /**
     * @var \Magento\CustomerSegment\Model\Resource\Segment\CollectionFactory
     */
    protected $_collectionFactory;

    /**
     * Store list manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magento\Framework\App\Http\Context
     */
    protected $_httpContext;

    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param Resource\Segment\CollectionFactory $collectionFactory
     * @param \Magento\Customer\Model\Resource\Customer $resourceCustomer
     * @param \Magento\Customer\Model\Config\Share $configShare
     * @param \Magento\Log\Model\Visitor $visitor
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Framework\App\Http\Context $httpContext
     * @param \Magento\Framework\Model\Resource\AbstractResource $resource
     * @param \Magento\Framework\Data\Collection\Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\CustomerSegment\Model\Resource\Segment\CollectionFactory $collectionFactory,
        \Magento\Customer\Model\Resource\Customer $resourceCustomer,
        \Magento\Customer\Model\Config\Share $configShare,
        \Magento\Log\Model\Visitor $visitor,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\App\Http\Context $httpContext,
        \Magento\Framework\Model\Resource\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\Db $resourceCollection = null,
        array $data = array()
    ) {
        $this->_storeManager = $storeManager;
        $this->_collectionFactory = $collectionFactory;
        $this->_resourceCustomer = $resourceCustomer;
        $this->_configShare = $configShare;
        $this->_visitor = $visitor;
        $this->_customerSession = $customerSession;
        $this->_httpContext = $httpContext;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Class constructor
     *
     * @return void
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
            $relatedSegments = $this->_collectionFactory->create()->addEventFilter(
                $eventName
            )->addWebsiteFilter(
                $websiteId
            )->addIsActiveFilter(
                1
            );
            $this->_segmentMap[$eventName][$websiteId] = $relatedSegments;
        }
        return $this->_segmentMap[$eventName][$websiteId];
    }

    /**
     * Match all related to event segments and assign/deassign customer/visitor to segments on specific website
     *
     * @param   string $eventName
     * @param   \Magento\Customer\Model\Customer|int $customer
     * @param   \Magento\Store\Model\Website|int $website
     * @return  $this
     */
    public function processEvent($eventName, $customer, $website)
    {
        \Magento\Framework\Profiler::start('__SEGMENTS_MATCHING__');

        $website = $this->_storeManager->getWebsite($website);
        $segments = $this->getActiveSegmentsForEvent($eventName, $website->getId());

        $this->_processSegmentsValidation($customer, $website, $segments);

        \Magento\Framework\Profiler::stop('__SEGMENTS_MATCHING__');
        return $this;
    }

    /**
     * Validate all segments for specific customer/visitor on specific website
     *
     * @param   \Magento\Customer\Model\Customer $customer
     * @param   \Magento\Store\Model\Website $website
     * @return  $this
     */
    public function processCustomer(\Magento\Customer\Model\Customer $customer, $website)
    {
        $website = $this->_storeManager->getWebsite($website);
        $segments = $this->_collectionFactory->create()->addWebsiteFilter($website)->addIsActiveFilter(1);

        $this->_processSegmentsValidation($customer, $website, $segments);

        return $this;
    }

    /**
     * Check if customer is related to segments and update customer-segment relations
     *
     * @param int|null|\Magento\Customer\Model\Customer $customer
     * @param \Magento\Store\Model\Website $website
     * @param \Magento\CustomerSegment\Model\Resource\Segment\Collection $segments
     * @return $this
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
                $segment->setVisitorId($this->_visitor->getId());
            } else {
                // Skip segment if it cannot be applied to customer
                if ($segment->getApplyTo() == \Magento\CustomerSegment\Model\Segment::APPLY_TO_VISITORS) {
                    continue;
                }
            }
            $isMatched = $segment->validateCustomer($customer, $website);
            if ($isMatched) {
                $matchedIds[] = $segment->getId();
            } else {
                $notMatchedIds[] = $segment->getId();
            }
        }


        if ($customerId) {
            $this->addCustomerToWebsiteSegments($customerId, $websiteId, $matchedIds);
            $this->removeCustomerFromWebsiteSegments($customerId, $websiteId, $notMatchedIds);
            $segmentIds = $this->_customerWebsiteSegments[$websiteId][$customerId];
        } else {
            $this->addVisitorToWebsiteSegments($this->_customerSession, $websiteId, $matchedIds);
            $this->removeVisitorFromWebsiteSegments($this->_customerSession, $websiteId, $notMatchedIds);
            $allSegments = $this->_customerSession->getCustomerSegmentIds();
            $segmentIds = $allSegments[$websiteId];
        }

        return $this;
    }

    /**
     * Match customer id to all segments related to event on all websites where customer can be presented
     *
     * @param string $eventName
     * @param int $customerId
     * @return $this
     */
    public function processCustomerEvent($eventName, $customerId)
    {
        if ($this->_configShare->isWebsiteScope()) {
            $websiteIds = $this->_resourceCustomer->getWebsiteId($customerId);
            if ($websiteIds) {
                $websiteIds = array($websiteIds);
            } else {
                $websiteIds = array();
            }
        } else {
            $websiteIds = $this->_storeManager->getWebsites();
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
     * @param \Magento\Framework\Session\SessionManagerInterface $visitorSession
     * @param int $websiteId
     * @param array $segmentIds
     * @return $this
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
        $this->_httpContext->setValue(Data::CONTEXT_SEGMENT, array_filter($visitorSegmentIds), array());
        return $this;
    }

    /**
     * Remove visitor-segment relation for specified website
     *
     * @param \Magento\Framework\Session\SessionManagerInterface $visitorSession
     * @param int $websiteId
     * @param array $segmentIds
     * @return $this
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
        $this->_httpContext->setValue(Data::CONTEXT_SEGMENT, array_filter($visitorCustomerSegmentIds), array());
        return $this;
    }

    /**
     * Add customer relation with segment for specific website
     *
     * @param int $customerId
     * @param int $websiteId
     * @param array $segmentIds
     * @return $this
     */
    public function addCustomerToWebsiteSegments($customerId, $websiteId, $segmentIds)
    {
        $existingIds = $this->getCustomerSegmentIdsForWebsite($customerId, $websiteId);
        $this->_getResource()->addCustomerToWebsiteSegments($customerId, $websiteId, $segmentIds);
        $this->_customerWebsiteSegments[$websiteId][$customerId] = array_unique(
            array_merge($existingIds, $segmentIds)
        );
        return $this;
    }

    /**
     * Remove customer id association with segment ids on specific website
     *
     * @param int $customerId
     * @param int $websiteId
     * @param array $segmentIds
     * @return $this
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
            $this->_customerWebsiteSegments[$websiteId][$customerId] = $this
                ->_getResource()
                ->getCustomerWebsiteSegments(
                    $customerId,
                    $websiteId
                );
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
        $websiteId = $this->_storeManager->getWebsite()->getId();
        if (!$customer->getId()) {
            $allSegmentIds = $customerSession->getCustomerSegmentIds();
            if (is_array($allSegmentIds) && isset($allSegmentIds[$websiteId])) {
                $result = $allSegmentIds[$websiteId];
            }
        } else {
            $result = $this->getCustomerSegmentIdsForWebsite(
                $customer->getId(),
                $this->_storeManager->getWebsite()->getId()
            );
        }
        return $result;
    }
}
