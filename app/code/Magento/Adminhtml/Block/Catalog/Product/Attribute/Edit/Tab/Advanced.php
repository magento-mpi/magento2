<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Product attribute add/edit form main tab
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Adminhtml_Block_Catalog_Product_Attribute_Edit_Tab_Advanced extends Magento_Backend_Block_Widget_Form
{
    /**
     * Adding product form elements for editing attribute
     *
     * @return Magento_Adminhtml_Block_Catalog_Product_Attribute_Edit_Tab_Advanced
     */
    protected function _prepareForm()
    {
        $attributeObject = $this->getAttributeObject();

        $form = new Magento_Data_Form(array(
            'id' => 'edit_form',
            'action' => $this->getData('action'),
            'method' => 'post'
        ));

        $fieldset = $form->addFieldset(
            'advanced_fieldset',
            array(
                'legend' => $this->__('Advanced Attribute Properties'),
                'collapsable' => true
            )
        );

        $yesno = Mage::getModel('Magento_Backend_Model_Config_Source_Yesno')->toOptionArray();

        $validateClass = sprintf(
            'validate-code validate-length maximum-length-%d',
            Magento_Eav_Model_Entity_Attribute::ATTRIBUTE_CODE_MAX_LENGTH
        );
        $fieldset->addField(
            'attribute_code',
            'text',
            array(
                'name' => 'attribute_code',
                'label' => $this->__('Attribute Code'),
                'title' => $this->__('Attribute Code'),
                'note' => $this->__(
                    'For internal use. Must be unique with no spaces. Maximum length of attribute code must be less than %s symbols',
                    Magento_Eav_Model_Entity_Attribute::ATTRIBUTE_CODE_MAX_LENGTH
                ),
                'class' => $validateClass,
            )
        );

        $fieldset->addField(
            'default_value_text',
            'text',
            array(
                'name' => 'default_value_text',
                'label' => $this->__('Default Value'),
                'title' => $this->__('Default Value'),
                'value' => $attributeObject->getDefaultValue(),
            )
        );

        $fieldset->addField(
            'default_value_yesno',
            'select',
            array(
                'name' => 'default_value_yesno',
                'label' => $this->__('Default Value'),
                'title' => $this->__('Default Value'),
                'values' => $yesno,
                'value' => $attributeObject->getDefaultValue(),
            )
        );

        $dateFormat = Mage::app()->getLocale()->getDateFormat(Magento_Core_Model_LocaleInterface::FORMAT_TYPE_SHORT);
        $fieldset->addField(
            'default_value_date',
            'date',
            array(
                'name' => 'default_value_date',
                'label' => $this->__('Default Value'),
                'title' => $this->__('Default Value'),
                'image' => $this->getViewFileUrl('images/grid-cal.gif'),
                'value' => $attributeObject->getDefaultValue(),
                'date_format' => $dateFormat
            )
        );

        $fieldset->addField(
            'default_value_textarea',
            'textarea',
            array(
                'name' => 'default_value_textarea',
                'label' => $this->__('Default Value'),
                'title' => $this->__('Default Value'),
                'value' => $attributeObject->getDefaultValue(),
            )
        );

        $fieldset->addField(
            'is_unique',
            'select',
            array(
                'name' => 'is_unique',
                'label' => $this->__('Unique Value'),
                'title' => $this->__('Unique Value (not shared with other products)'),
                'note' => $this->__('Not shared with other products'),
                'values' => $yesno,
            )
        );

        $fieldset->addField(
            'frontend_class',
            'select',
            array(
                'name' => 'frontend_class',
                'label' => $this->__('Input Validation for Store Owner'),
                'title' => $this->__('Input Validation for Store Owner'),
                'values' => Mage::helper('Magento_Eav_Helper_Data')->getFrontendClasses(
                    $attributeObject->getEntityType()->getEntityTypeCode()
                )
            )
        );

        if ($attributeObject->getId()) {
            $form->getElement('attribute_code')->setDisabled(1);
            if (!$attributeObject->getIsUserDefined()) {
                $form->getElement('is_unique')->setDisabled(1);
            }
        }

        $yesnoSource = Mage::getModel('Magento_Backend_Model_Config_Source_Yesno')->toOptionArray();

        $scopes = array(
            Magento_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE =>Mage::helper('Magento_Catalog_Helper_Data')->__('Store View'),
            Magento_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE =>Mage::helper('Magento_Catalog_Helper_Data')->__('Website'),
            Magento_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL =>Mage::helper('Magento_Catalog_Helper_Data')->__('Global'),
        );

        if ($attributeObject->getAttributeCode() == 'status' || $attributeObject->getAttributeCode() == 'tax_class_id') {
            unset($scopes[Magento_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE]);
        }

        $fieldset->addField('is_global', 'select', array(
            'name'  => 'is_global',
            'label' => Mage::helper('Magento_Catalog_Helper_Data')->__('Scope'),
            'title' => Mage::helper('Magento_Catalog_Helper_Data')->__('Scope'),
            'note'  => Mage::helper('Magento_Catalog_Helper_Data')->__('Declare attribute value saving scope'),
            'values'=> $scopes
        ), 'attribute_code');


        $fieldset->addField('is_configurable', 'select', array(
            'name' => 'is_configurable',
            'label' => Mage::helper('Magento_Catalog_Helper_Data')->__('Use To Create Configurable Product'),
            'values' => $yesnoSource,
        ));
        $this->setForm($form);
        return $this;
    }

    /**
     * Initialize form fileds values
     *
     * @return Magento_Adminhtml_Block_Catalog_Product_Attribute_Edit_Tab_Advanced
     */
    protected function _initFormValues()
    {
        $this->getForm()->addValues($this->getAttributeObject()->getData());
        return parent::_initFormValues();
    }

    /**
     * Retrieve attribute object from registry
     *
     * @return Magento_Eav_Model_Entity_Attribute_Abstract
     */
    private function getAttributeObject()
    {
        return Mage::registry('entity_attribute');
    }
}
