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
 * @package    Enterprise_Banner
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://www.magentocommerce.com/license/enterprise-edition
 */

class Enterprise_Banner_Model_Mysql4_Catalogrule_Collection extends Mage_CatalogRule_Model_Mysql4_Rule_Collection
{
    /**
     * Reset collection columns
     *
     * @return Enterprise_Banner_Model_Mysql4_Catalogrule_Collection
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
     * @param string $now
     * @return Enterprise_Banner_Model_Mysql4_Catalogrule_Collection
     */
    public function setRuleValidationFilter($websiteId, $customerGroupId, $now=null)
    {
        $select = $this->getSelect();
        if (is_null($now)) {
            $now = Mage::getModel('core/date')->date('Y-m-d');
        }
        $select->where('is_active=1');
        $select->where('find_in_set(?, website_ids)', (int)$websiteId);
        $select->where('find_in_set(?, customer_group_ids)', (int)$customerGroupId);
        $select->where('from_date is null or from_date<=?', $now);
        $select->where('to_date is null or to_date>=?', $now);

        return $this;
    }

    /**
     * Set related banners to catalog rule
     *
     * @param bool $enabledOnly if true then only enabled banners will be joined
     * @return Enterprise_Banner_Model_Mysql4_Catalogrule_Collection
     */
    public function setBannersFilter($enabledOnly = false)
    {
        $select = $this->getSelect();
        $select->join(
                array('banner_rules' => $this->getTable('enterprise_banner/catalogrule')),
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