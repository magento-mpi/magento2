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
 * @package     Enterprise_CustomerSegment
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */

class Enterprise_CustomerSegment_Block_Adminhtml_Customersegment_Edit_Tab_Conditions extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * Prepare conditions form
     *
     * @return Enterprise_CustomerSegment_Block_Adminhtml_Customersegment_Edit_Tab_Conditions
     */
    protected function _prepareForm()
    {
        $model = Mage::registry('current_customer_segment');

        $form = new Varien_Data_Form();

        $form->setHtmlIdPrefix('segment_');

        $renderer = Mage::getBlockSingleton('Mage_Adminhtml_Block_Widget_Form_Renderer_Fieldset')
            ->setTemplate('promo/fieldset.phtml')
            ->setNewChildUrl($this->getUrl('*/customersegment/newConditionHtml/form/segment_conditions_fieldset'));
        $fieldset = $form->addFieldset('conditions_fieldset', array(
            'legend'=>Mage::helper('Enterprise_CustomerSegment_Helper_Data')->__('Conditions'))
        )->setRenderer($renderer);

        $fieldset->addField('conditions', 'text', array(
            'name' => 'conditions',
            'label' => Mage::helper('Enterprise_CustomerSegment_Helper_Data')->__('Conditions'),
            'title' => Mage::helper('Enterprise_CustomerSegment_Helper_Data')->__('Conditions'),
            'required' => true,
        ))->setRule($model)->setRenderer(Mage::getBlockSingleton('Mage_Rule_Block_Conditions'));

        $form->setValues($model->getData());
        $this->setForm($form);

        return parent::_prepareForm();
    }

}
