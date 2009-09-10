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

/**
 * TargetRule observer
 *
 */
class Enterprise_TargetRule_Model_Observer
{
    /**
     * Prepare target rule data
     *
     * @param Varien_Event_Observer $observer
     */
    public function prepareTargetRuleSave(Varien_Event_Observer $observer)
    {
        $_vars = array('targetrule_rule_based_positions', 'targetrule_position_behavior');
        $_varPrefix = array('related_', 'upsell_', 'crosssell_');
        if ($product = $observer->getEvent()->getProduct()) {
            foreach ($_vars as $var) {
                foreach ($_varPrefix as $pref) {
                    $v = $pref . $var;
                    if ($product->getData($v.'_default') == 1) {
                        $product->setData($v, null);
                    }
                }
            }
        }
    }

    /**
     * Add additional field to edit product attribute form
     *
     * @param Varien_Event_Observer $observer
     */
    public function addProductAttributeField(Varien_Event_Observer $observer)
    {
        /* @var $form Varien_Data_Form */
        $form = $observer->getForm();
        /* @var $fieldset Varien_Data_Form_Element_Fieldset */
        $fieldset = $form->getElement('front_fieldset');
        $fieldset->addField('is_used_for_target_rules', 'select', array(
            'name' => 'is_used_for_target_rules',
            'label' => Mage::helper('enterprise_targetrule')->__('Use for Target Rule Conditions'),
            'title' => Mage::helper('enterprise_targetrule')->__('Use for Target Rule Conditions'),
            'values' => Mage::getModel('adminhtml/system_config_source_yesno')->toOptionArray(),
        ), 'is_used_for_price_rules');
    }

}
