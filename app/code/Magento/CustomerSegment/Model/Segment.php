<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
/**
 * Enterprise CustomerSegment Segment Model
 *
 * @method \Magento\CustomerSegment\Model\Resource\Segment _getResource()
 * @method \Magento\CustomerSegment\Model\Resource\Segment getResource()
 * @method string getName()
 * @method \Magento\CustomerSegment\Model\Segment setName(string $value)
 * @method string getDescription()
 * @method \Magento\CustomerSegment\Model\Segment setDescription(string $value)
 * @method int getIsActive()
 * @method \Magento\CustomerSegment\Model\Segment setIsActive(int $value)
 * @method string getConditionsSerialized()
 * @method \Magento\CustomerSegment\Model\Segment setConditionsSerialized(string $value)
 * @method int getProcessingFrequency()
 * @method \Magento\CustomerSegment\Model\Segment setProcessingFrequency(int $value)
 * @method string getConditionSql()
 * @method \Magento\CustomerSegment\Model\Segment setConditionSql(string $value)
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\CustomerSegment\Model;

class Segment extends \Magento\Rule\Model\AbstractModel
{
    /**
     * Customer segment view modes
     */
    const VIEW_MODE_UNION_CODE = 'union';

    const VIEW_MODE_INTERSECT_CODE = 'intersect';

    /**
     * Possible states of customer segment
     */
    const APPLY_TO_VISITORS = 2;

    const APPLY_TO_REGISTERED = 1;

    const APPLY_TO_VISITORS_AND_REGISTERED = 0;

    /**
     * @var \Magento\Rule\Model\Action\CollectionFactory
     */
    protected $_collectionFactory;

    /**
     * @var \Magento\Customer\Model\Visitor
     */
    protected $_visitor;

    /**
     * @var \Magento\Customer\Model\VisitorFactory
     */
    protected $_visitorFactory;

    /**
     * @var \Magento\CustomerSegment\Model\ConditionFactory
     */
    protected $_conditionFactory;

    /**
     * Store list manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Rule\Model\Action\CollectionFactory $collectionFactory
     * @param \Magento\Customer\Model\Visitor $visitor
     * @param \Magento\Customer\Model\VisitorFactory $visitorFactory
     * @param \Magento\CustomerSegment\Model\ConditionFactory $conditionFactory
     * @param \Magento\Framework\Model\Resource\AbstractResource $resource
     * @param \Magento\Framework\Data\Collection\Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Rule\Model\Action\CollectionFactory $collectionFactory,
        \Magento\Customer\Model\Visitor $visitor,
        \Magento\Customer\Model\VisitorFactory $visitorFactory,
        \Magento\CustomerSegment\Model\ConditionFactory $conditionFactory,
        \Magento\Framework\Model\Resource\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\Db $resourceCollection = null,
        array $data = []
    ) {
        $this->_storeManager = $storeManager;
        $this->_collectionFactory = $collectionFactory;
        $this->_visitor = $visitor;
        $this->_visitorFactory = $visitorFactory;
        $this->_conditionFactory = $conditionFactory;
        parent::__construct($context, $registry, $formFactory, $localeDate, $resource, $resourceCollection, $data);
    }

    /**
     * Set resource model
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_init('Magento\CustomerSegment\Model\Resource\Segment');
    }

    /**
     * Set aggregated conditions SQL.
     * Collect and save list of events which are applicable to segment.
     *
     * @return \Magento\CustomerSegment\Model\Segment
     */
    public function beforeSave()
    {
        if (!$this->getData('processing_frequency')) {
            $this->setData('processing_frequency', '1');
        }

        if (!$this->isObjectNew()) {
            // Keep 'apply_to' property without changes for existing customer segments
            $this->setData('apply_to', $this->getOrigData('apply_to'));
        }

        $events = [];
        if ($this->getIsActive()) {
            $events = $this->collectMatchedEvents();
        }
        $customer = new \Zend_Db_Expr(':customer_id');
        $website = new \Zend_Db_Expr(':website_id');
        $this->setConditionSql($this->getConditions()->getConditionsSql($customer, $website));
        $this->setMatchedEvents(array_unique($events));

        parent::beforeSave();
        return $this;
    }

    /**
     * Getter for rule combine conditions instance
     *
     * @return \Magento\CustomerSegment\Model\Segment\Condition\Combine
     */
    public function getConditionsInstance()
    {
        return $this->_conditionFactory->create('Combine\Root');
    }

    /**
     * Getter for rule actions collection instance
     *
     * @return \Magento\Rule\Model\Action\Collection
     */
    public function getActionsInstance()
    {
        return $this->_collectionFactory->create();
    }

    /**
     * Collect all matched event names for current segment
     *
     * @param null|\Magento\CustomerSegment\Model\Condition\Combine\AbstractCombine $conditionsCombine
     *
     * @return array
     */
    public function collectMatchedEvents($conditionsCombine = null)
    {
        $events = [];
        if ($conditionsCombine === null) {
            $conditionsCombine = $this->getConditions();
        }
        $matchedEvents = $conditionsCombine->getMatchedEvents();
        if (!empty($matchedEvents)) {
            $events = array_merge($events, $matchedEvents);
        }
        $children = $conditionsCombine->getConditions();
        if ($children) {
            if (!is_array($children)) {
                $children = [$children];
            }
            foreach ($children as $child) {
                $events = array_merge($events, $this->collectMatchedEvents($child));
            }
        }

        if ($this->getApplyToo() != self::APPLY_TO_REGISTERED) {
            $events = array_merge($events, ['visitor_init']);
        }

        $events = array_unique($events);

        return $events;
    }

    /**
     * Get list of all models which are used in segment conditions
     *
     * @param  null|\Magento\Rule\Model\Condition\Combine $conditions
     *
     * @return array
     */
    public function getConditionModels($conditions = null)
    {
        $models = [];

        if (is_null($conditions)) {
            $conditions = $this->getConditions();
        }

        $models[] = $conditions->getType();
        $childConditions = $conditions->getConditions();
        if ($childConditions) {
            if (is_array($childConditions)) {
                foreach ($childConditions as $child) {
                    $models = array_merge($models, $this->getConditionModels($child));
                }
            } else {
                $models = array_merge($models, $this->getConditionModels($childConditions));
            }
        }

        return $models;
    }

    /**
     * Validate customer by segment conditions for current website
     *
     * @param \Magento\Framework\Object $object
     *
     * @return bool
     */
    public function validate(\Magento\Framework\Object $object)
    {
        $website = $this->_storeManager->getWebsite();
        if ($object instanceof \Magento\Customer\Model\Customer) {
            if (!$object->getId()) {
                $this->setVisitorId($this->_visitor->getId());
            }
            return $this->validateCustomer($object, $website);
        }
        return false;
    }

    /**
     * Check if customer is matched by segment
     *
     * @param int|\Magento\Customer\Model\Customer|\Magento\Framework\Object $customer
     * @param null|\Magento\Store\Model\Website|bool|int|string $website
     *
     * @return bool
     */
    public function validateCustomer($customer, $website)
    {
        /**
         * Use prepared in beforeSave sql
         */
        $sql = $this->getConditionSql();
        if (!$sql) {
            return false;
        }
        if ($customer instanceof \Magento\Customer\Model\Customer) {
            $customerId = $customer->getId();
        } else {
            $customerId = $customer;
        }

        $params = [];
        if (strpos($sql, ':customer_id')) {
            $params['customer_id'] = $customerId;
        }
        if (strpos($sql, ':website_id')) {
            $params['website_id'] = $this->_storeManager->getWebsite($website)->getId();
        }
        if (strpos($sql, ':quote_id')) {
            if (!$customerId) {
                $params['quote_id'] = $this->_visitorFactory->create()->load($this->getVisitorId())->getQuoteId();
            } else {
                $params['quote_id'] = 0;
            }
        }
        if (strpos($sql, ':visitor_id')) {
            if (!$customerId) {
                $params['visitor_id'] = $this->getVisitorId();
            } else {
                $params['visitor_id'] = 0;
            }
        }
        $result = $this->getResource()->runConditionSql($sql, $params);
        return $result > 0;
    }

    /**
     * Match all customers by segment conditions and fill customer/segments relations table
     *
     * @return \Magento\CustomerSegment\Model\Segment
     */
    public function matchCustomers()
    {
        $this->_getResource()->aggregateMatchedCustomers($this);
        return $this;
    }
}
