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
 * Cart coupon code condition
 */
class Enterprise_Reminder_Model_Rule_Condition_Cart_Couponcode
    extends Enterprise_Reminder_Model_Condition_Abstract
{
    public function __construct()
    {
        parent::__construct();
        $this->setType('enterprise_reminder/rule_condition_cart_couponcode');
        $this->setValue(1);
    }

    /**
     * Get inherited conditions selectors
     *
     * @return array
     */
    public function getNewChildSelectOptions()
    {
        return array('value' => $this->getType(),
            'label' => Mage::helper('enterprise_reminder')->__('Coupon Code'));
    }

    /**
     * Get HTML of condition string
     *
     * @return string
     */
    public function asHtml()
    {
        return $this->getTypeElementHtml()
            . Mage::helper('enterprise_reminder')->__('Shopping cart %s a coupon applied',
                $this->getValueElementHtml())
            . $this->getRemoveLinkHtml();
    }

    /**
     * Get element type for value select
     *
     * @return string
     */
    public function getValueElementType()
    {
        return 'select';
    }

    /**
     * Init list of available values
     *
     * @return Enterprise_Reminder_Model_Rule_Condition_Cart_Couponcode
     */
    public function loadValueOptions()
    {
        $this->setValueOption(array(
            '1' => Mage::helper('enterprise_reminder')->__('has'),
            '0' => Mage::helper('enterprise_reminder')->__('does not have')
        ));
        return $this;
    }

    /**
     * Get SQL select
     *
     * @param $customer
     * @param int | Zend_Db_Expr $website
     * @return Varien_Db_Select
     */
    public function getConditionsSql($customer, $website)
    {
        $table = $this->getResource()->getTable('sales/quote');
        $inversion = ((int)$this->getValue() ? '' : '!');

        $select = $this->getResource()->createSelect();
        $select->from(array('quote' => $table), array(new Zend_Db_Expr(1)));

        $this->_limitByStoreWebsite($select, $website, 'quote.store_id');
        $select->where('quote.is_active = 1');
        $select->where("{$inversion}(IFNULL(quote.coupon_code, '') <> '')");
        $select->where($this->_createCustomerFilter($customer, 'quote.customer_id'));
        $select->limit(1);

        return $select;
    }
}
