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
     * Set related banners to sales rule
     *
     * @param bool $enabledOnly if true then only enabled banners will be joined
     * @return Enterprise_Banner_Model_Mysql4_Salesrule_Collection
     */
    public function setBannersFilter($enabledOnly = false)
    {
        $this->getSelect()
             ->join(
                array('banner_rules' => $this->getTable('enterprise_banner/salesrule')),
                'banner_rules.rule_id = main_table.rule_id',
                array('banner_id')
             );
        if ($enabledOnly) {
            $this->getSelect()
                 ->join(
                    array('banners' => $this->getTable('enterprise_banner/banner')),
                    'banners.banner_id = banner_rules.banner_id AND banners.is_enabled=1',
                    array()
                 );
        }
        $this->getSelect()->group('banner_rules.banner_id');
        return $this;
    }
}