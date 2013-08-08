<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Customer
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Form Type Edit Form Block
 *
 * @category   Enterprise
 * @package    Enterprise_Customer
 */
class Enterprise_Customer_Block_Adminhtml_Customer_Formtype_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * Retrieve current form type instance
     *
     * @return Mage_Eav_Model_Form_Type
     */
    protected function _getFormType()
    {
        return Mage::registry('current_form_type');
    }

    /**
     * Prepare form before rendering HTML
     *
     * @return Enterprise_Customer_Block_Adminhtml_Customer_Formtype_Edit_Form
     */
    protected function _prepareForm()
    {
        $editMode = Mage::registry('edit_mode');
        if ($editMode == 'edit') {
            $saveUrl = $this->getUrl('*/*/save');
            $showNew = false;
        } else {
            $saveUrl = $this->getUrl('*/*/create');
            $showNew = true;
        }
        $form = new Magento_Data_Form(array(
            'id'        => 'edit_form',
            'action'    => $saveUrl,
            'method'    => 'post'
        ));

        if ($showNew) {
            $fieldset = $form->addFieldset('base_fieldset', array(
                'legend' => Mage::helper('Enterprise_Customer_Helper_Data')->__('General Information'),
                'class'  => 'fieldset-wide'
            ));

            $options = $this->_getFormType()->getCollection()->toOptionArray();
            array_unshift($options, array(
                'label' => Mage::helper('Enterprise_Customer_Helper_Data')->__('-- Please Select --'),
                'value' => ''
            ));
            $fieldset->addField('type_id', 'select', array(
                'name'      => 'type_id',
                'label'     => Mage::helper('Enterprise_Customer_Helper_Data')->__('Based On'),
                'title'     => Mage::helper('Enterprise_Customer_Helper_Data')->__('Based On'),
                'required'  => true,
                'values'    => $options
            ));

            $fieldset->addField('label', 'text', array(
                'name'      => 'label',
                'label'     => Mage::helper('Enterprise_Customer_Helper_Data')->__('Form Label'),
                'title'     => Mage::helper('Enterprise_Customer_Helper_Data')->__('Form Label'),
                'required'  => true,
            ));

            /** @var $label Mage_Core_Model_Theme_Label */
            $label = Mage::getModel('Mage_Core_Model_Theme_Label');
            $options = $label->getLabelsCollection();
            array_unshift($options, array(
                'label' => Mage::helper('Enterprise_Customer_Helper_Data')->__('All Themes'),
                'value' => ''
            ));
            $fieldset->addField('theme', 'select', array(
                'name'      => 'theme',
                'label'     => Mage::helper('Enterprise_Customer_Helper_Data')->__('For Theme'),
                'title'     => Mage::helper('Enterprise_Customer_Helper_Data')->__('For Theme'),
                'values'    => $options
            ));

            $fieldset->addField('store_id', 'select', array(
                'name'      => 'store_id',
                'label'     => Mage::helper('Enterprise_Customer_Helper_Data')->__('Store View'),
                'title'     => Mage::helper('Enterprise_Customer_Helper_Data')->__('Store View'),
                'required'  => true,
                'values'    => Mage::getSingleton('Mage_Core_Model_System_Store')->getStoreValuesForForm(false, true)
            ));

            $form->setValues($this->_getFormType()->getData());
        }

        $form->setUseContainer(true);
        $this->setForm($form);
        return parent::_prepareForm();
    }
}
