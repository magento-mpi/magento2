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
 * @package     Enterprise_Customer
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */


/**
 * Customer Address Attribute General Tab Block
 *
 * @category    Enterprise
 * @package     Enterprise_Customer
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Customer_Block_Adminhtml_Customer_Address_Attribute_Edit_Tab_General
    extends Enterprise_Enterprise_Block_Eav_Adminhtml_Attribute_Edit_Main_Abstract
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    /**
     * Adding customer address attribute form elements for edit form
     *
     * @return Enterprise_Customer_Block_Adminhtml_Customer_Address_Attribute_Edit_Tab_General
     */
    protected function _prepareForm()
    {
    parent::_prepareForm();

        $attribute  = $this->getAttributeObject();
        $form       = $this->getForm();
        $fieldset   = $form->getElement('base_fieldset');
        /* @var $helper Enterprise_Customer_Helper_Data */
        $helper     = Mage::helper('enterprise_customer');

        $fieldset->removeField('frontend_class');
        $fieldset->removeField('is_unique');

        // update Input Types
        $values     = $helper->getFrontendInputOptions();
        $element    = $form->getElement('frontend_input');
        $element->setValues($values);
        $element->setLabel(Mage::helper('enterprise_customer')->__('Input type'));
        $element->setRequired(true);

        $fieldset->addField('multiline_count', 'text', array(
            'name'      => 'multiline_count',
            'label'     => Mage::helper('enterprise_customer')->__('Lines Count'),
            'title'     => Mage::helper('enterprise_customer')->__('Lines Count'),
            'required'  => true,
            'class'     => 'validate-digits',
        ), 'frontend_input');

        $fieldset->addField('input_validation', 'select', array(
            'name'      => 'input_validation',
            'label'     => Mage::helper('enterprise_customer')->__('Input Validation'),
            'title'     => Mage::helper('enterprise_customer')->__('Input Validation'),
            'values'    => array('' => Mage::helper('enterprise_customer')->__('None'))
        ), 'default_value_textarea');

        $fieldset->addField('min_text_length', 'text', array(
            'name'      => 'min_text_length',
            'label'     => Mage::helper('enterprise_customer')->__('Minimum Length of Text'),
            'title'     => Mage::helper('enterprise_customer')->__('Minimum Length of Text'),
            'class'     => 'validate-digits',
        ), 'input_validation');

        $fieldset->addField('max_text_length', 'text', array(
            'name'      => 'max_text_length',
            'label'     => Mage::helper('enterprise_customer')->__('Maximum Length of Text'),
            'title'     => Mage::helper('enterprise_customer')->__('Maximum Length of Text'),
            'class'     => 'validate-digits',
        ), 'min_text_length');

        $fieldset->addField('max_file_size', 'text', array(
            'name'      => 'max_file_size',
            'label'     => Mage::helper('enterprise_customer')->__('Maximum Size of File (bytes)'),
            'title'     => Mage::helper('enterprise_customer')->__('Maximum Size of File (bytes)'),
            'class'     => 'validate-digits',
        ), 'max_text_length');

        $fieldset->addField('max_image_width', 'text', array(
            'name'      => 'max_image_width',
            'label'     => Mage::helper('enterprise_customer')->__('Maximum Image Width (px)'),
            'title'     => Mage::helper('enterprise_customer')->__('Maximum Image Width (px)'),
            'class'     => 'validate-digits',
        ), 'max_file_size');

        $fieldset->addField('max_image_heght', 'text', array(
            'name'      => 'max_image_heght',
            'label'     => Mage::helper('enterprise_customer')->__('Maximum Image Height (px)'),
            'title'     => Mage::helper('enterprise_customer')->__('Maximum Image Height (px)'),
            'class'     => 'validate-digits',
        ), 'max_image_width');

        $fieldset->addField('input_filter', 'select', array(
            'name'      => 'input_filter',
            'label'     => Mage::helper('enterprise_customer')->__('Input/Output Filter'),
            'title'     => Mage::helper('enterprise_customer')->__('Input/Output Filter'),
            'values'    => array('' => Mage::helper('enterprise_customer')->__('None')),
        ));

        $yesnoSource = Mage::getModel('adminhtml/system_config_source_yesno')->toOptionArray();

        $fieldset = $form->addFieldset('front_fieldset', array(
            'legend'    => Mage::helper('enterprise_customer')->__('Frontend Properties')
        ));

        $fieldset->addField('is_visible', 'select', array(
            'name'      => 'is_visible',
            'label'     => Mage::helper('enterprise_customer')->__('Show on the Frontend'),
            'title'     => Mage::helper('enterprise_customer')->__('Show on the Frontend'),
            'values'    => $yesnoSource,
        ));

        $fieldset->addField('sort_order', 'text', array(
            'name'      => 'sort_order',
            'label'     => Mage::helper('enterprise_customer')->__('Sort Order'),
            'title'     => Mage::helper('enterprise_customer')->__('Sort Order'),
            'required'  => true,
            'class'     => 'validate-digits'
        ));

        $fieldset->addField('used_in_forms', 'multiselect', array(
            'name'      => 'used_in_forms',
            'label'     => Mage::helper('enterprise_customer')->__('Forms to Use In'),
            'title'     => Mage::helper('enterprise_customer')->__('Forms to Use In'),
            'values'    => $helper->getCustomerAddressAttributeFormOptions(),
            'value'     => $attribute->getUsedInForms()
        ))->setSize(5);

        if ($attribute->getId()) {
            $elements = array();
            if ($attribute->getIsSystem()) {
                $elements = array('sort_order', 'is_visible', 'is_required', 'used_in_forms');
            }
            if (!$attribute->getIsUserDefined() && !$attribute->getIsSystem()) {
                $elements = array('sort_order', 'used_in_forms');
            }
            foreach ($elements as $elementId) {
                $form->getElement($elementId)->setDisabled(true);
            }

            $inputTypeProp = $helper->getAttributeInputTypes($attribute->getFrontendInput());

            // input_filter
            if ($inputTypeProp['filter_types']) {
                $filterTypes = $helper->getAttributeFilterTypes();
                $values = $form->getElement('input_filter')->getValues();
                foreach ($inputTypeProp['filter_types'] as $filterTypeCode) {
                    $values[$filterTypeCode] = $filterTypes[$filterTypeCode];
                }
                $form->getElement('input_filter')->setValues($values);
            }

            // input_validation getAttributeValidateFilters
            if ($inputTypeProp['validate_filters']) {
                $filterTypes = $helper->getAttributeValidateFilters();
                $values = $form->getElement('input_validation')->getValues();
                foreach ($inputTypeProp['validate_filters'] as $filterTypeCode) {
                    $values[$filterTypeCode] = $filterTypes[$filterTypeCode];
                }
                $form->getElement('input_validation')->setValues($values);
            }
        }

        return $this;
    }

    /**
     * Initialize form fileds values
     *
     * @return Mage_Eav_Block_Adminhtml_Attribute_Edit_Main_Abstract
     */
    protected function _initFormValues()
    {
        $attribute = $this->getAttributeObject();
        if ($attribute->getId() && $attribute->getValidateRules()) {
            $this->getForm()->addValues($attribute->getValidateRules());
        }
        return parent::_initFormValues();
    }

    /**
     * Return Tab label
     *
     * @return string
     */
    public function getTabLabel()
    {
        return Mage::helper('enterprise_customer')->__('Properties');
    }

    /**
     * Return Tab title
     *
     * @return string
     */
    public function getTabTitle()
    {
        return Mage::helper('enterprise_customer')->__('Properties');
    }

    /**
     * Can show tab in tabs
     *
     * @return boolean
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Tab is hidden
     *
     * @return boolean
     */
    public function isHidden()
    {
        return false;
    }
}