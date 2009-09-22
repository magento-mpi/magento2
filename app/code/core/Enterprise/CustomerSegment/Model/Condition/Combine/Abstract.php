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
 * @category   Enterprise
 * @package    Enterprise_CustomerSegment
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://www.magentocommerce.com/license/enterprise-edition
 */

class Enterprise_CustomerSegment_Model_Condition_Combine_Abstract extends Mage_Rule_Model_Condition_Combine
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

    protected function _prepareConditionsSql($customer) {
        $select = $this->getResource()->createSelect();

        $select->from($this->getResource()->getTable('customer/entity'), array(new Zend_Db_Expr(1)));
        $select->where('entity_id = ?', $customer->getId());

        return $select;
    }

    protected function _getRequiredValidation()
    {
        return ($this->getValue() == 1);
    }

    public function getConditionsSql($customer)
    {
        $select = $this->_prepareConditionsSql($customer);

        if ($this->getAggregator() == 'all') {
            $whereFunction = 'where';
        } else {
            $whereFunction = 'orWhere';
        }

        if ($this->_getRequiredValidation()) {
            $operator = '=';
        } else {
            $operator = '<>';
        }

        $gotConditions = false;

        $children = $this->_getChildConditionsSql($customer);
        if ($children) {
            foreach ($children as $criteria) {
                if ($criteria) {
                    $criteriaSql = "(IFNULL(($criteria), 0) {$operator} 1)";

                    $select->$whereFunction($criteriaSql);

                    $gotConditions = true;
                }
            }
        }

        foreach ($this->getConditions() as $condition) {
            if ($condition->getSubfilterType()) {
                switch ($condition->getSubfilterType()) {
                    case 'date':
                        $subfilter = $condition->getSubfilterSql($this->_getDateSubfilterField(), $this->_getRequiredValidation());
                        if ($subfilter) {
                            $select->$whereFunction($subfilter);
                            $gotConditions = true;
                        }
                        break;

                    case 'product':
                        $subfilter = $condition->getSubfilterSql($this->_getProductSubfilterField(), $this->_getRequiredValidation());
                        if ($subfilter) {
                            $select->$whereFunction($subfilter);
                            $gotConditions = true;
                        }
                        break;
                }
            }
        }

        if (!$gotConditions) {
            $select->where('1=1');
        }

        return $select;
    }


    protected function _getChildConditionsSql($customer)
    {
        $result = array();
        foreach ($this->getConditions() as $condition) {
            if ($sql = $condition->getConditionsSql($customer)) {
                $result[] = $sql;
            }
        }
        return $result;
    }
}
