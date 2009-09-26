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
 * @package     Enterprise_Banner
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */

class Enterprise_Banner_Model_Mysql4_Salesrule_Collection extends Mage_SalesRule_Model_Mysql4_Rule_Collection
{
    /**
     * Reset collection columns
     *
     * @return Enterprise_Banner_Model_Mysql4_Salesrule_Collection
     */
    public function resetColumns()
    {
        $this->getSelect()->reset(Zend_Db_Select::COLUMNS);
        return $this;
    }

    /**
     * Apply only valid rules
     *
     * @param int $websiteId
     * @param int $customerGroupId
     * @param int $customerId
     * @param string $now
     * @return Enterprise_Banner_Model_Mysql4_Salesrule_Collection
     */
    public function setRuleValidationFilter($websiteId, $customerGroupId, $customerId, $now=null)
    {
        $select = $this->getSelect();
        if (is_null($now)) {
            $now = Mage::getModel('core/date')->date('Y-m-d');
        }

        //Join salesrule customer to check times used per customer
        $select->joinLeft(
            array('customer_rules' => $this->getTable('salesrule/rule_customer')),
            $this->getConnection()->quoteInto('(customer_rules.rule_id = main_table.rule_id AND customer_rules.customer_id = ?)', $customerId),
            array()
        );

        //Coupon code validation
        $select->where("(
                           (coupon_code != '' AND coupon_code IS NOT NULL) AND
                           (
                               (main_table.times_used < uses_per_coupon) AND
                               (customer_rules.rule_customer_id IS NULL OR customer_rules.times_used < uses_per_customer)
                           )
                       ) OR
                       (coupon_code = '') OR
                       (coupon_code IS NULL)");

        $select->where('is_active=1');
        $select->where('find_in_set(?, website_ids)', (int)$websiteId);
        $select->where('find_in_set(?, customer_group_ids)', (int)$customerGroupId);
        $select->where('from_date is null or from_date<=?', $now);
        $select->where('to_date is null or to_date>=?', $now);
        $select->order('sort_order');

        return $this;
    }

    /**
     * Set related banners to sales rule
     *
     * @param bool $enabledOnly if true then only enabled banners will be joined
     * @return Enterprise_Banner_Model_Mysql4_Salesrule_Collection
     */
    public function setBannersFilter($enabledOnly = false)
    {
        $select = $this->getSelect();
        $select->join(
            array('banner_rules' => $this->getTable('enterprise_banner/salesrule')),
            'banner_rules.rule_id = main_table.rule_id',
            array('banner_id')
        );
        if ($enabledOnly) {
            $select->join(
                array('banners' => $this->getTable('enterprise_banner/banner')),
                'banners.banner_id = banner_rules.banner_id AND banners.is_enabled=1',
                array()
            );
        }
        $select->group('banner_rules.banner_id');
        return $this;
    }
}
