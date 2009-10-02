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

abstract class Enterprise_CustomerSegment_Model_Condition_Combine_Abstract extends Mage_Rule_Model_Condition_Combine
{
    /**
     * Get array of event names where segment with such conditions combine can be matched
     *
     * @return array
     */
    public function getMatchedEvents()
    {
        return array();
    }

    /**
     * Customize default operator input by type mapper for some types
     *
     * @return array
     */
    public function getDefaultOperatorInputByType()
    {
        if (null === $this->_defaultOperatorInputByType) {
            parent::getDefaultOperatorInputByType();
            $this->_defaultOperatorInputByType['numeric'] = array('==', '!=', '>=', '>', '<=', '<');
            $this->_defaultOperatorInputByType['string'] = array('==', '!=', '{}', '!{}');
        }
        return $this->_defaultOperatorInputByType;
    }

    /**
     * Add operator when loading array
     *
     * @param array $arr
     * @param string $key
     * @return Enterprise_CustomerSegment_Model_Segment_Condition_Combine
     */
    public function loadArray($arr, $key = 'conditions')
    {
        if (isset($arr['operator'])) {
            $this->setOperator($arr['operator']);
        }

        if (isset($arr['attribute'])) {
            $this->setAttribute($arr['attribute']);
        }

        return parent::loadArray($arr, $key);
    }

    /**
     * Get condition combine resource model
     *
     * @return Enterprise_CustomerSegment_Model_Mysql4_Segment
     */
    public function getResource()
    {
        return Mage::getResourceSingleton('enterprise_customersegment/segment');
    }

    /**
     * Get filter by customer condition for segment matching sql
     *
     * @param $customer
     * @param string $fieldName
     * @return string
     */
    protected function _createCustomerFilter($customer, $fieldName)
    {
        return "{$fieldName} = root.entity_id";
    }

    /**
     * Build query for matching customer to segment condition
     *
     * @param $customer
     * @param $website
     * @return Varien_Db_Select
     */
    protected function _prepareConditionsSql($customer, $website)
    {
        $select = $this->getResource()->createSelect();
        $table = $this->getResource()->getTable('customer/entity');
        $select->from($table, array(new Zend_Db_Expr(1)));
        $select->where($this->_createCustomerFilter($customer, 'entity_id'));
        return $select;
    }

    protected function _getRequiredValidation()
    {
        return ($this->getValue() == 1);
    }

    /**
     * Get SQL select for matching customer to segment condition
     *
     * @param $customer
     * @param $website
     * @return unknown_type
     */
    public function getConditionsSql($customer, $website)
    {
        $select = $this->_prepareConditionsSql($customer, $website);
        $required = $this->_getRequiredValidation();

        if ($this->getAggregator() == 'all') {
            $whereFunction = 'where';
        } else {
            $whereFunction = 'orWhere';
        }

        if ($required) {
            $operator = '=';
        } else {
            $operator = '<>';
        }

        $gotConditions = false;

        foreach ($this->getConditions() as $condition) {
            if ($sql = $condition->getConditionsSql($customer, $website)) {
                $criteriaSql = "(IFNULL(($sql), 0) {$operator} 1)";
                $select->$whereFunction($criteriaSql);
                $gotConditions = true;
            }
        }

        $subfilterMap = $this->_getSubfilterMap();
        if ($subfilterMap) {
            foreach ($this->getConditions() as $condition) {
                $subfilterType = $condition->getSubfilterType();
                if (isset($subfilterMap[$subfilterType])) {
                    $subfilter = $condition->getSubfilterSql($subfilterMap[$subfilterType], $required, $website);

                    if ($subfilter) {
                        $select->$whereFunction($subfilter);
                        $gotConditions = true;
                    }
                }
            }
        }

        if (!$gotConditions) {
            $select->where('1=1');
        }

        return $select;
    }

    protected function _getSubfilterMap()
    {
        return array();
    }
}
