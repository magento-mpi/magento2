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
 * @package    Enterprise_TargetRule
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://www.magentocommerce.com/license/enterprise-edition
 */

class Enterprise_TargetRule_Model_Rule extends Mage_Rule_Model_Rule
{
    const BOTH_SELECTED_AND_RULE_BASED = 0;
    const SELECTED_ONLY = 1;
    const RULE_BASED_ONLY = 2;

    const RELATED_PRODUCTS = 0;
    const UP_SELLS = 1;
    const CROSS_SELLS = 2;

    const CONFIG_VALUES_XPATH = 'catalog/enterprise_targetrule/';

    /**
     * Init resource model
     */
    public function __construct()
    {
        $this->_init('enterprise_targetrule/rule');
    }

    /**
     * Return conditions instance
     *
     * @return Enterprise_TargetRule_Model_Rule_Condition_Combine
     */
    public function getConditionsInstance()
    {
        return Mage::getModel('enterprise_targetrule/rule_condition_combine');
    }

    /**
     * Get options for `Apply to` field
     *
     * @return array
     */
    public function getAppliesToOptions()
    {
        return array(
                Enterprise_TargetRule_Model_Rule::RELATED_PRODUCTS
                    => Mage::helper('enterprise_targetrule')->__('Related Products'),
                Enterprise_TargetRule_Model_Rule::UP_SELLS
                    => Mage::helper('enterprise_targetrule')->__('Up-sells'),
                Enterprise_TargetRule_Model_Rule::CROSS_SELLS
                    => Mage::helper('enterprise_targetrule')->__('Cross-sells'),
            );
    }
}
