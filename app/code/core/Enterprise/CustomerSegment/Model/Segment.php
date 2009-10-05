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
class Enterprise_CustomerSegment_Model_Segment extends Mage_Rule_Model_Rule
{
    /**
     * Intialize model
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_init('enterprise_customersegment/segment');
    }

    /**
     * Return conditions instance
     *
     * @return Enterprise_CustomerSegment_Model_Segment_Condition_Combine
     */
    public function getConditionsInstance()
    {
        return Mage::getModel('enterprise_customersegment/segment_condition_combine_root');
    }

    /**
     * Perform actions after object load
     */
    protected function _afterLoad()
    {
        Mage_Core_Model_Abstract::_afterLoad();
        $conditionsArr = unserialize($this->getConditionsSerialized());
        if (!empty($conditionsArr) && is_array($conditionsArr)) {
            $this->getConditions()->loadArray($conditionsArr);
        }
        return $this;
    }

    /**
     * Perform actions before object save.
     * Collect and save list of events which are applicable to segment.
     */
    protected function _beforeSave()
    {
        if (!$this->getData('processing_frequency')){
            $this->setData('processing_frequency', '1');
        }

        $events = array();
        if ($this->getIsActive()) {
            $events = $this->collectMatchedEvents();
        }
        $customer = new Zend_Db_Expr(':customer_id');
        $website = Mage::app()->getWebsite($this->getWebsiteId());
        $this->setConditionSql(
            $this->getConditions()->getConditionsSql($customer, $website)
        );
        $this->setMatchedEvents(array_unique($events));
        parent::_beforeSave();
    }

    /**
     * Collect all matched event names for segment
     *
     * @param null | Enterprise_CustomerSegment_Model_Condition_Combine_Abstract $conditionsCombine
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
        $events = array_unique($events);
        return $events;
    }

    /**
     * Get list of all models which are used in segment conditions
     *
     * @param  null | Mage_Rule_Model_Condition_Combine $conditions
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
     * Validate customer by segment conditions for ran website
     *
     * @param Varien_Object $object
     * @return bool
     */
    public function validate(Varien_Object $object)
    {
        $website = Mage::app()->getWebsite();
        if ($object instanceof Mage_Customer_Model_Customer) {
            return $this->validateCustomer($object, $website);
        }
        return false;
    }

    /**
     * Check if customer is matched by segment
     *
     * @param Mage_Customer_Model_Customer $customer
     * @param $website
     * @return bool
     */
    public function validateCustomer(Mage_Customer_Model_Customer $customer, $website)
    {
        /**
         * Use prepeared in beforeSave sql
         */
        $sql = $this->getConditionSql();
        $result = $this->getResource()->runConditionSql($sql, array('customer_id'=>$customer->getId()));
        return $result>0;
    }

    /**
     * Match all customers by segment conditions and fill customer/segments relations table
     *
     * @return Enterprise_CustomerSegment_Model_Segment
     */
    public function matchCustomers()
    {
        $website = Mage::app()->getWebsite($this->getWebsiteId());
        $sql = $this->getConditions()->getConditionsSql(null, $website);
        $this->_getResource()->beginTransaction();
        try {
            $this->_getResource()->saveSegmentCustomersFromSelect($this, $sql);
            $this->_getResource()->commit();
        } catch (Exception $e) {
            echo $e;die();
            $this->_getResource()->rollBack();
        }
        return $this;
    }
}
