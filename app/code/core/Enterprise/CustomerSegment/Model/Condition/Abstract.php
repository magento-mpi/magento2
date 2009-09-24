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

class Enterprise_CustomerSegment_Model_Condition_Abstract extends Mage_Rule_Model_Condition_Abstract
{
    /**
     * Remove "in" and "not in" for numeric operator options
     */
    public function __construct()
    {
        parent::__construct();
        $this->_defaultOperatorInputByType['numeric'] = array('==', '!=', '>=', '>', '<=', '<');
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
}
