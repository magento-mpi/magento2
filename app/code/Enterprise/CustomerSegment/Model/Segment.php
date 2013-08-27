<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_CustomerSegment
 * @copyright   {copyright}
 * @license     {license_link}
 */
/**
 * Enterprise CustomerSegment Segment Model
 *
 * @method Enterprise_CustomerSegment_Model_Resource_Segment _getResource()
 * @method Enterprise_CustomerSegment_Model_Resource_Segment getResource()
 * @method string getName()
 * @method Enterprise_CustomerSegment_Model_Segment setName(string $value)
 * @method string getDescription()
 * @method Enterprise_CustomerSegment_Model_Segment setDescription(string $value)
 * @method int getIsActive()
 * @method Enterprise_CustomerSegment_Model_Segment setIsActive(int $value)
 * @method string getConditionsSerialized()
 * @method Enterprise_CustomerSegment_Model_Segment setConditionsSerialized(string $value)
 * @method int getProcessingFrequency()
 * @method Enterprise_CustomerSegment_Model_Segment setProcessingFrequency(int $value)
 * @method string getConditionSql()
 * @method Enterprise_CustomerSegment_Model_Segment setConditionSql(string $value)
 *
 * @category    Enterprise
 * @package     Enterprise_CustomerSegment
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_CustomerSegment_Model_Segment extends Magento_Rule_Model_Abstract
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
     * Set resource model
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_init('Enterprise_CustomerSegment_Model_Resource_Segment');
    }

    /**
     * Set aggregated conditions SQL.
     * Collect and save list of events which are applicable to segment.
     *
     * @return Enterprise_CustomerSegment_Model_Segment
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
     * @return Enterprise_CustomerSegment_Model_Segment_Condition_Combine
     */
    public function getConditionsInstance()
    {
        return Mage::getModel('Enterprise_CustomerSegment_Model_Segment_Condition_Combine_Root');
    }

    /**
     * Getter for rule actions collection instance
     *
     * @return Magento_Rule_Model_Action_Collection
     */
    public function getActionsInstance()
    {
        return Mage::getModel('Magento_Rule_Model_Action_Collection');
    }

    /**
     * Collect all matched event names for current segment
     *
     * @param null|Enterprise_CustomerSegment_Model_Condition_Combine_Abstract $conditionsCombine
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
        $website = Mage::app()->getWebsite();
        if ($object instanceof Magento_Customer_Model_Customer) {
            if (!$object->getId()) {
                $this->setVisitorId(Mage::getSingleton('Magento_Log_Model_Visitor')->getId());
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

        $website = Mage::app()->getWebsite($website);
        $params = array();
        if (strpos($sql, ':customer_id')) {
            $params['customer_id']  = $customerId;
        }
        if (strpos($sql, ':website_id')) {
            $params['website_id']   = $website->getId();
        }
        if (strpos($sql, ':quote_id')) {
            if (!$customerId) {
                $params['quote_id'] = Mage::getModel('Magento_Log_Model_Visitor')
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
     * @return Enterprise_CustomerSegment_Model_Segment
     */
    public function matchCustomers()
    {
        $this->_getResource()->aggregateMatchedCustomers($this);
        return $this;
    }
}
