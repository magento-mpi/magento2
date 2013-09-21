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
 * Enterprise CustomerSegment Segment Model
 *
 * @method Magento_CustomerSegment_Model_Resource_Segment _getResource()
 * @method Magento_CustomerSegment_Model_Resource_Segment getResource()
 * @method string getName()
 * @method Magento_CustomerSegment_Model_Segment setName(string $value)
 * @method string getDescription()
 * @method Magento_CustomerSegment_Model_Segment setDescription(string $value)
 * @method int getIsActive()
 * @method Magento_CustomerSegment_Model_Segment setIsActive(int $value)
 * @method string getConditionsSerialized()
 * @method Magento_CustomerSegment_Model_Segment setConditionsSerialized(string $value)
 * @method int getProcessingFrequency()
 * @method Magento_CustomerSegment_Model_Segment setProcessingFrequency(int $value)
 * @method string getConditionSql()
 * @method Magento_CustomerSegment_Model_Segment setConditionSql(string $value)
 *
 * @category    Magento
 * @package     Magento_CustomerSegment
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_CustomerSegment_Model_Segment extends Magento_Rule_Model_Abstract
{
    /**
     * Customer segment view modes
     */
    const VIEW_MODE_UNION_CODE      = 'union';
    const VIEW_MODE_INTERSECT_CODE  = 'intersect';

    /**
     * Possible states of customer segment
     */
    const APPLY_TO_VISITORS = 2;
    const APPLY_TO_REGISTERED = 1;
    const APPLY_TO_VISITORS_AND_REGISTERED = 0;

    /**
     * @var Magento_Rule_Model_Action_CollectionFactory
     */
    protected $_collectionFactory;

    /**
     * @var Magento_Log_Model_Visitor
     */
    protected $_visitor;

    /**
     * @var Magento_Log_Model_VisitorFactory
     */
    protected $_visitorFactory;

    /**
     * @var Magento_CustomerSegment_Model_ConditionFactory
     */
    protected $_conditionFactory;

    /**
     * Store list manager
     *
     * @var Magento_Core_Model_StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @param Magento_Core_Model_StoreManagerInterface $storeManager
     * @param Magento_Rule_Model_Action_CollectionFactory $collectionFactory
     * @param Magento_Log_Model_Visitor $visitor
     * @param Magento_Log_Model_VisitorFactory $visitorFactory
     * @param Magento_CustomerSegment_Model_ConditionFactory $conditionFactory
     * @param Magento_Data_Form_Factory $formFactory
     * @param Magento_Core_Model_Context $context
     * @param Magento_Core_Model_Registry $registry
     * @param Magento_Core_Model_Resource_Abstract $resource
     * @param Magento_Data_Collection_Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        Magento_Core_Model_StoreManagerInterface $storeManager,
        Magento_Rule_Model_Action_CollectionFactory $collectionFactory,
        Magento_Log_Model_Visitor $visitor,
        Magento_Log_Model_VisitorFactory $visitorFactory,
        Magento_CustomerSegment_Model_ConditionFactory $conditionFactory,
        Magento_Data_Form_Factory $formFactory,
        Magento_Core_Model_Context $context,
        Magento_Core_Model_Registry $registry,
        Magento_Core_Model_Resource_Abstract $resource = null,
        Magento_Data_Collection_Db $resourceCollection = null,
        array $data = array()
    ) {
        $this->_storeManager = $storeManager;
        $this->_collectionFactory = $collectionFactory;
        $this->_visitor = $visitor;
        $this->_visitorFactory = $visitorFactory;
        $this->_conditionFactory = $conditionFactory;
        parent::__construct($formFactory, $context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Set resource model
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_init('Magento_CustomerSegment_Model_Resource_Segment');
    }

    /**
     * Set aggregated conditions SQL.
     * Collect and save list of events which are applicable to segment.
     *
     * @return Magento_CustomerSegment_Model_Segment
     */
    protected function _beforeSave()
    {
        if (!$this->getData('processing_frequency')) {
            $this->setData('processing_frequency', '1');
        }

        if (!$this->isObjectNew()) {
            // Keep 'apply_to' property without changes for existing customer segments
            $this->setData('apply_to', $this->getOrigData('apply_to'));
        }

        $events = array();
        if ($this->getIsActive()) {
            $events = $this->collectMatchedEvents();
        }
        $customer = new Zend_Db_Expr(':customer_id');
        $website = new Zend_Db_Expr(':website_id');
        $this->setConditionSql(
            $this->getConditions()->getConditionsSql($customer, $website)
        );
        $this->setMatchedEvents(array_unique($events));

        parent::_beforeSave();
        return $this;
    }

    /**
     * Getter for rule combine conditions instance
     *
     * @return Magento_CustomerSegment_Model_Segment_Condition_Combine
     */
    public function getConditionsInstance()
    {
        return $this->_conditionFactory->create('Combine_Root');
    }

    /**
     * Getter for rule actions collection instance
     *
     * @return Magento_Rule_Model_Action_Collection
     */
    public function getActionsInstance()
    {
        return $this->_collectionFactory->create();
    }

    /**
     * Collect all matched event names for current segment
     *
     * @param null|Magento_CustomerSegment_Model_Condition_Combine_Abstract $conditionsCombine
     *
     * @return array
     */
    public function collectMatchedEvents($conditionsCombine = null)
    {
        $events = array();
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
                $children = array($children);
            }
            foreach ($children as $child) {
                $events = array_merge($events, $this->collectMatchedEvents($child));
            }
        }

        if ($this->getApplyToo() != self::APPLY_TO_REGISTERED) {
            $events = array_merge($events, array('visitor_init'));
        }

        $events = array_unique($events);

        return $events;
    }

    /**
     * Get list of all models which are used in segment conditions
     *
     * @param  null|Magento_Rule_Model_Condition_Combine $conditions
     *
     * @return array
     */
    public function getConditionModels($conditions = null)
    {
        $models = array();

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
     * @param Magento_Object $object
     *
     * @return bool
     */
    public function validate(Magento_Object $object)
    {
        $website = $this->_storeManager->getWebsite();
        if ($object instanceof Magento_Customer_Model_Customer) {
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
     * @param int|Magento_Customer_Model_Customer|Magento_Object $customer
     * @param null|Magento_Core_Model_Website|bool|int|string $website
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
        if ($customer instanceof Magento_Customer_Model_Customer) {
            $customerId = $customer->getId();
        } else {
            $customerId = $customer;
        }

        $params = array();
        if (strpos($sql, ':customer_id')) {
            $params['customer_id']  = $customerId;
        }
        if (strpos($sql, ':website_id')) {
            $params['website_id'] = $this->_storeManager->getWebsite($website)->getId();
        }
        if (strpos($sql, ':quote_id')) {
            if (!$customerId) {
                $params['quote_id'] = $this->_visitorFactory->create()
                    ->load($this->getVisitorId())->getQuoteId();
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
     * @return Magento_CustomerSegment_Model_Segment
     */
    public function matchCustomers()
    {
        $this->_getResource()->aggregateMatchedCustomers($this);
        return $this;
    }
}
