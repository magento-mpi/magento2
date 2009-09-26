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

    public function getResource()
    {
        return Mage::getResourceSingleton('enterprise_customersegment/segment');
    }

    protected function _getSqlOperator()
    {
        /*
            '{}'  => Mage::helper('rule')->__('contains'),
            '!{}' => Mage::helper('rule')->__('does not contain'),
            '()'  => Mage::helper('rule')->__('is one of'),
            '!()' => Mage::helper('rule')->__('is not one of'),

            requires custom selects
        */

        switch ($this->getOperator()) {
            case "==":
                return '=';

            case "!=":
                return '<>';

            case ">":
            case "<":
            case ">=":
            case "<=":
                return $this->getOperator();

            default:
                Mage::throwException(Mage::helper('enterprise_customersegment')->__('Unknown operator specified'));
        }
    }

    protected function _createCustomerFilter($customer, $fieldName, $isRoot)
    {
        if ($isRoot) {
            if ($customer instanceof Mage_Customer_Model_Customer) {
                $customer = $customer->getId();
            } else if ($customer instanceof Zend_Db_Select) {
                $customer = new Zend_Db_Expr($customer);
            }

            return $this->getResource()->quoteInto("{$fieldName} IN (?)", $customer);
        } else {
            return "{$fieldName} = root.entity_id";
        }
    }

    protected function _prepareConditionsSql($customer, $isRoot) {
        $select = $this->getResource()->createSelect();

        if ($isRoot) {
            $table = array('root' => $this->getResource()->getTable('customer/entity'));
        } else {
            $table = $this->getResource()->getTable('customer/entity');
        }

        $select->from($table, array(new Zend_Db_Expr(1)));
        $select->where($this->_createCustomerFilter($customer, 'entity_id', $isRoot));

        return $select;
    }

    protected function _getRequiredValidation()
    {
        return ($this->getValue() == 1);
    }

    public function getConditionsSql($customer, $isRoot = true)
    {
        $select = $this->_prepareConditionsSql($customer, $isRoot);
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
            if ($sql = $condition->getConditionsSql($customer, false)) {
                $criteriaSql = "(IFNULL(($sql), 0) {$operator} 1)";
                $select->$whereFunction($criteriaSql);
                $gotConditions = true;
            }
        }

        foreach ($this->getConditions() as $condition) {
            $sufilter = false;
            switch ($condition->getSubfilterType()) {
                case 'date':
                    $subfilter = $condition->getSubfilterSql($this->_getDateSubfilterField(), $required);
                    break;
                case 'product':
                    $subfilter = $condition->getSubfilterSql($this->_getProductSubfilterField(), $required);
                    break;
                case 'order_status':
                    $subfilter = $condition->getSubfilterSql($this->_getOrderSubfilterField(), $required);
                    break;
                case 'order_address_type':
                    $subfilter = $condition->getSubfilterSql($this->_getOrderAddressTypeSubfilterField(), $required);
                    break;
            }
            if ($subfilter) {
                $select->$whereFunction($subfilter);
                $gotConditions = true;
            }
        }

        if (!$gotConditions) {
            $select->where('1=1');
        }

        return $select;
    }
}
