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

class Enterprise_TargetRule_Block_Adminhtml_Targetrule_Edit_Tab_Conditions extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $model = Mage::registry('current_target_rule');

        $form = new Varien_Data_Form();

        $form->setHtmlIdPrefix('rule_');

        $renderer = Mage::getBlockSingleton('adminhtml/widget_form_renderer_fieldset')
            ->setTemplate('enterprise/targetrule/edit/conditions/fieldset.phtml')
            ->setNewChildUrl($this->getUrl('*/targetrule/newConditionHtml/'));

        $fieldset = $form->addFieldset('conditions_fieldset', array(
            'legend'=>Mage::helper('enterprise_targetrule')->__('Conditions (leave blank for all products)'))
        )->setRenderer($renderer);

        $fieldset->addField('conditions', 'text', array(
            'name' => 'conditions',
            'label' => Mage::helper('enterprise_targetrule')->__('Conditions'),
            'title' => Mage::helper('enterprise_targetrule')->__('Conditions'),
            'required' => true,
        ))->setRule($model)->setRenderer(Mage::getBlockSingleton('rule/conditions'));

//Product Attribute
//Customer Segment
        $form->setValues($model->getData());

        $this->setForm($form);

        return parent::_prepareForm();
    }
}
