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
class Enterprise_Reminder_Model_Rule_Condition_Wishlist
    extends Enterprise_Reminder_Model_Condition_Combine_Abstract
{
    /**
     * class constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->setType('enterprise_reminder/rule_condition_wishlist');
        $this->setValue(null);
    }

    /**
     * Get list of available subconditions
     *
     * @return array
     */
    public function getNewChildSelectOptions()
    {
        $prefix = 'enterprise_reminder/rule_condition_wishlist_';
        $result = array_merge_recursive(parent::getNewChildSelectOptions(), array(
            array( // subconditions combo
                'value' => 'enterprise_reminder/rule_condition_wishlist_combine',
                'label' => Mage::helper('enterprise_reminder')->__('Conditions Combination')),

            array( // subselection combo
                'value' => 'enterprise_reminder/rule_condition_wishlist_subselection',
                'label' => Mage::helper('enterprise_reminder')->__('Wishlist Item Subselection')),

            Mage::getModel($prefix.'abandoned')->getNewChildSelectOptions(),
            Mage::getModel($prefix.'sharing')->getNewChildSelectOptions(),
            Mage::getModel($prefix.'quantity')->getNewChildSelectOptions(),

        ));
        return $result;
    }

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
            . Mage::helper('enterprise_reminder')->__('Wishlist has items and %s of these conditions match:',
                $this->getAggregatorElement()->getHtml())
            . $this->getRemoveLinkHtml();
    }

    protected function _prepareConditionsSql($customer, $website)
    {
        $wishlistTable = $this->getResource()->getTable('wishlist/wishlist');
        $wishlistItemTable = $this->getResource()->getTable('wishlist/item');

        $select = $this->getResource()->createSelect();
        $select->from(array('item'=>$wishlistItemTable), array(new Zend_Db_Expr(1)));

        $select->joinInner(
            array('list' => $wishlistTable),
            'item.wishlist_id = list.wishlist_id',
            array()
        );

        $select->where($this->_createCustomerFilter($customer, 'list.customer_id'));
        $select->limit(1);
        return $select;
    }

    public function getConditionsSql1($customer, $website)
    {
        /**
         * Build base SQL
         */
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
