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
 * Resource collection of customers matched by reminder rule
 */
class Enterprise_Reminder_Model_Mysql4_Customer_Collection
    extends Mage_Customer_Model_Entity_Customer_Collection
{
    /**
     * Instantiate select to get matched customers
     *
     * @return Enterprise_Reminder_Model_Mysql4_Customer_Collection
     */
    protected function _initSelect()
    {
        parent::_initSelect();

        $rule = Mage::registry('current_reminder_rule');
        $select = $this->getSelect();

        $couponTable = $this->getTable('enterprise_reminder/coupon');
        $ruleTable = $this->getTable('enterprise_reminder/rule');
        $logTable = $this->getTable('enterprise_reminder/log');
        $salesRuleCouponTable = $this->getTable('salesrule/coupon');

        $select->joinInner(
            array('c' => $couponTable),
            'e.entity_id=c.customer_id AND c.is_active=1',
            array('associated_at')
        );

        $select->joinInner(
            array('r' => $ruleTable),
            'c.rule_id=r.rule_id AND c.rule_id=' . $rule->getId(),
            array('schedule' => new Zend_Db_Expr('IF(r.schedule != \'\', 1, 0)'))
        );

        $select->joinLeft(
            array('sc' => $salesRuleCouponTable),
            'c.coupon_id=sc.coupon_id',
            array('code', 'usage_limit', 'usage_per_customer')
        );

        $select->joinLeft(
            array('l' => $logTable),
            'l.rule_id=c.rule_id AND l.customer_id=e.entity_id',
            array(
                'emails_sent' => new Zend_Db_Expr('COUNT(l.log_id)'),
                'last_sent' => new Zend_Db_Expr('MAX(l.sent_at)')
            )
        );

        $select->group('e.entity_id');

        $this->_joinFields['associated_at'] = array('table'=>'c', 'field' => 'associated_at');
        $this->_joinFields['schedule'] = array('table'=>'r', 'field' => 'schedule');
        $this->_joinFields['code'] = array('table'=>'sc', 'field' => 'code');
        $this->_joinFields['usage_limit'] = array('table'=>'sc', 'field' => 'usage_limit');
        $this->_joinFields['usage_per_customer'] = array('table'=>'sc', 'field' => 'usage_per_customer');
        $this->_joinFields['emails_sent'] = array('table'=>'', 'field' => 'emails_sent');
        $this->_joinFields['last_sent'] = array('table'=>'', 'field' => 'last_sent');

        return $this;
    }

    /**
     * Get SQL for get record count
     *
     * @return Varien_Db_Select
     */
    public function getSelectCountSql()
    {
        $countSelect = parent::getSelectCountSql();
        return $countSelect->reset(Zend_Db_Select::GROUP);
    }
}
