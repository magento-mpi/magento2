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
 * Customer wishlist conditions combine
 */
class Enterprise_Reminder_Model_Rule_Condition_Cart
    extends Enterprise_Reminder_Model_Condition_Combine_Abstract
{
    protected $_inputType = 'numeric';

    /**
     * class constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->setType('enterprise_reminder/rule_condition_cart');
        $this->setValue(null);
    }

    /**
     * Get list of available subconditions
     *
     * @return array
     */
    public function getNewChildSelectOptions()
    {
        $prefix = 'enterprise_reminder/rule_condition_cart_';
        $result = array_merge_recursive(parent::getNewChildSelectOptions(), array(
            array( // subconditions combo
                'value' => 'enterprise_reminder/rule_condition_cart_combine',
                'label' => Mage::helper('rule')->__('Conditions Combination')),

            array( // subselection combo
                'value' => 'enterprise_reminder/rule_condition_cart_subselection',
                'label' => Mage::helper('enterprise_reminder')->__('Shopping Cart Item Subselection')),

            Mage::getModel($prefix.'couponcode')->getNewChildSelectOptions(),
            Mage::getModel($prefix.'itemsquantity')->getNewChildSelectOptions(),
            Mage::getModel($prefix.'totalquantity')->getNewChildSelectOptions(),
            Mage::getModel($prefix.'virtual')->getNewChildSelectOptions(),
            Mage::getModel($prefix.'amount')->getNewChildSelectOptions()
        ));
        return $result;
    }

    /**
     * Get input type for attribute value
     *
     * @return string
     */
    public function getValueElementType()
    {
        return 'text';
    }

     /**
     * Load value options
     *
     * @return Enterprise_Reminder_Model_Rule_Condition_Cart
     */
    public function loadValueOptions()
    {
        return $this;
    }

    /**
     * Return required validation
     *
     * @return true
     */
    protected function _getRequiredValidation()
    {
        return true;
    }

    /**
     * Get HTML of condition string
     *
     * @return string
     */
    public function asHtml()
    {
        return $this->getTypeElementHtml()
            . Mage::helper('enterprise_reminder')->__('Shopping Cart has items, abandoned for %s %s days and %s of these conditions match:',
                $this->getOperatorElementHtml(),
                $this->getValueElementHtml(),
                $this->getAggregatorElement()->getHtml())
            . $this->getRemoveLinkHtml();
    }

    /**
     * Get condition SQL select
     *
     * @param $customer
     * @param $website
     * @return Varien_Db_Select
     */
    protected function _prepareConditionsSql($customer, $website)
    {
        $table = $this->getResource()->getTable('sales/quote');
        $operator = $this->getResource()->getSqlOperator($this->getOperator());

        $select = $this->getResource()->createSelect();
        $select->from(array('quote'=>$table), array(new Zend_Db_Expr(1)));

        $this->_limitByStoreWebsite($select, $website, 'quote.store_id');
        $select->where("UNIX_TIMESTAMP('".now()."' - INTERVAL ? DAY) {$operator} UNIX_TIMESTAMP(quote.updated_at)", $this->getValue());
        $select->where('quote.is_active = 1');
        $select->where('quote.items_count > 0');
        $select->where($this->_createCustomerFilter($customer, 'quote.customer_id'));
        $select->limit(1);
        return $select;
    }

    /**
     * Get base SQL select
     *
     * @param $customer
     * @param $website
     * @return Varien_Db_Select
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
