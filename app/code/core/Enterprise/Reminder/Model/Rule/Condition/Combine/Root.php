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
 * @package     Enterprise_Reminder
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */

/**
 * Root rule condition (top level condition)
 */
class Enterprise_Reminder_Model_Rule_Condition_Combine_Root
    extends Enterprise_Reminder_Model_Rule_Condition_Combine
{
    /**
     * Class constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->setType('enterprise_reminder/rule_condition_combine_root');
    }

    /**
     * Prepare filter condition by customer
     *
     * @param int|array|Mage_Customer_Model_Customer|Zend_Db_Select $customer
     * @param string $fieldName
     * @return string
     */
    protected function _createCustomerFilter($customer, $fieldName)
    {
        if ($customer instanceof Mage_Customer_Model_Customer) {
            $customer = $customer->getId();
        } else if ($customer instanceof Zend_Db_Select) {
            $customer = new Zend_Db_Expr($customer);
        }

        return $this->getResource()->quoteInto("{$fieldName} IN (?)", $customer);
    }

    /**
     * Prepare base select with limitation by customer
     *
     * @param   null | array | int | Mage_Customer_Model_Customer $customer
     * @param   int | Zend_Db_Expr $website
     * @return  Varien_Db_Select
     */
    protected function _prepareConditionsSql($customer, $website)
    {
        $select = $this->getResource()->createSelect();
        $table = array('root' => $this->getResource()->getTable('customer/entity'));
        $select->from($table, array('entity_id'));

        if ($customer === null) {
            if (Mage::getSingleton('customer/config_share')->isWebsiteScope()) {
                $select->where('website_id=?', $website);
            }
        }
        return $select;
    }

    /**
     * Get SQL select.
     * Rewrited for cover root conditions combination with additional condition by customer
     *
     * @param   Mage_Customer_Model_Customer | Zend_Db_Select | Zend_Db_Expr $customer
     * @param   int | Zend_Db_Expr $website
     * @return  Varien_Db_Select
     */
    public function getConditionsSql($customer, $website)
    {
        $select     = $this->_prepareConditionsSql($customer, $website);
        $required   = $this->_getRequiredValidation();
        $aggregator = ($this->getAggregator() == 'all') ? ' AND ' : ' OR ';
        $operator   = $required ? '=' : '<>';
        $conditions = array();

        foreach ($this->getConditions() as $condition) {
            if ($sql = $condition->getConditionsSql($customer, $website)) {
                $conditions[] = "(IFNULL(($sql), 0) {$operator} 1)";
            }
        }

        if (!empty($conditions)) {
            $select->where(implode($aggregator, $conditions));
        }

        return $select;
    }
}
