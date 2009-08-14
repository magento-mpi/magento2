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
 * @package    Enterprise_Customer
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://www.magentocommerce.com/license/enterprise-edition
 */


/**
 * Customer Attributes Edit Form
 *
 * @category   Enterprise
 * @package    Enterprise_Customer
 */
class Enterprise_Customer_Block_Adminhtml_Customer_Attribute_Edit_Tab_Main extends Mage_Eav_Block_Adminhtml_Attribute_Edit_Main_Abstract
{
    /**
     * Adding customer form elements for edit form
     *
     * @return Enterprise_Customer_Block_Adminhtml_Customer_Attribute_Edit_Tab_Main
     */
    protected function _prepareForm()
    {
        parent::_prepareForm();
        $attributeObject = $this->getAttributeObject();
        $form = $this->getForm();
        $fieldset = $form->getElement('base_fieldset');

        $yesnoSource = Mage::getModel('adminhtml/system_config_source_yesno')->toOptionArray();

        $frontendInputElm = $form->getElement('frontend_input');
        $additionalTypes = array(
            array(
                'value' => 'radio',
                'label' => Mage::helper('enterprise_customer')->__('Radio')
            ),
            array(
                'value' => 'checkbox',
                'label' => Mage::helper('enterprise_customer')->__('Checkbox')
            ),
            array(
                'value' => 'date_range',
                'label' => Mage::helper('enterprise_customer')->__('Date Range')
            ),
            array(
                'value' => 'datetime',
                'label' => Mage::helper('enterprise_customer')->__('Datetime')
            ),
            array(
                'value' => 'multiline',
                'label' => Mage::helper('enterprise_customer')->__('Multiline')
            )
        );
        $frontendInputValues = array_merge($frontendInputElm->getValues(), $additionalTypes);
        $frontendInputElm->setValues($frontendInputValues);

        $fieldset->addField('lines_to_divide_multiline', 'text', array(
            'name' => 'lines_to_divide_multiline',
            'label' => Mage::helper('enterprise_customer')->__('Lines to Divide Text'),
            'title' => Mage::helper('enterprise_customer')->__('Lines to Divide Text'),
        ), 'frontend_input');

        $fieldset->addField('min_text_length', 'text', array(
            'name' => 'min_text_length',
            'label' => Mage::helper('enterprise_customer')->__('Minimum Length of Text'),
            'title' => Mage::helper('enterprise_customer')->__('Minimum Length of Text'),
        ), 'default_value_textarea');

        $fieldset->addField('max_text_length', 'text', array(
            'name' => 'max_text_length',
            'label' => Mage::helper('enterprise_customer')->__('Maximum Length of Text'),
            'title' => Mage::helper('enterprise_customer')->__('Maximum Length of Text'),
        ), 'min_text_length');

        $fieldset->addField('input_filter', 'multiselect', array(
            'name' => 'input_filter',
            'label' => Mage::helper('enterprise_customer')->__('Input Filters'),
            'values' => array(
                array(
                    'value' => 'trim',
                    'label' => Mage::helper('enterprise_customer')->__('Trim spaces')
                ),
                array(
                    'value' => 'replace_non_alphanum',
                    'label' => Mage::helper('enterprise_customer')->__('Replace Non Alphanumeric Characters')
                ),
                array(
                    'value' => 'strip_html',
                    'label' => Mage::helper('enterprise_customer')->__('Strip HTML')
                )
            ),
        ), 'frontend_class')->setSize(4);

        $fieldset = $form->addFieldset('front_fieldset', array('legend'=>Mage::helper('enterprise_customer')->__('Frontend Properties')));

        $fieldset->addField('is_visible_on_front', 'select', array(
            'name' => 'is_visible_on_front',
            'label' => Mage::helper('enterprise_customer')->__('Show On Frontend'),
            'title' => Mage::helper('enterprise_customer')->__('Show On Frontend'),
            'values' => $yesnoSource,
        ));

        $formTypesValues = Mage::getResourceModel('eav/form_type_collection')
            ->addEntityTypeFilter($attributeObject->getEntityType())->toOptionArray();

        $fieldset->addField('used_in', 'multiselect', array(
            'name' => 'used_in',
            'label' => Mage::helper('enterprise_customer')->__('Forms To Use In'),
            'title' => Mage::helper('enterprise_customer')->__('Forms To Use In'),
            'values' => $formTypesValues
        ));

        return $this;
    }

    /**
     * Initialize form fileds values.
     * Adding saved form types (used_in element) values for attribute
     *
     * @return Enterprise_Customer_Block_Adminhtml_Customer_Attribute_Edit_Tab_Main
     */
    protected function _initFormValues()
    {
        $this->getForm()->addValues(array(
            'used_in' => Mage::helper('enterprise_customer')
                ->getAttributeFormTypeIds($this->getAttributeObject())
        ));
        return parent::_initFormValues();
    }
}