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
 * Form Type Edit General Tab Block
 *
 * @category   Enterprise
 * @package    Enterprise_Customer
 */
class Enterprise_Customer_Block_Adminhtml_Customer_Formtype_Edit_Tab_General
    extends Mage_Adminhtml_Block_Widget_Form
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    /**
     * Initialize Edit Form
     *
     */
    protected function _construct()
    {
        $this->setDestElementId('edit_form');
        $this->setShowGlobalIcon(false);
        parent::_construct();
    }

    /**
     * Prepare form before rendering HTML
     *
     * @return Enterprise_Customer_Block_Adminhtml_Customer_Formtype_Grid_Tab_General
     */
    protected function _prepareForm()
    {
        /* @var $model Mage_Eav_Model_Form_Type */
        $model      = Mage::registry('current_form_type');

        $form       = new Varien_Data_Form();
        $fieldset   = $form->addFieldset('general_fieldset', array(
            'legend'    => Mage::helper('Enterprise_Customer_Helper_Data')->__('General Information')
        ));

        $fieldset->addField('continue_edit', 'hidden', array(
            'name'      => 'continue_edit',
            'value'     => 0
        ));
        $fieldset->addField('type_id', 'hidden', array(
            'name'      => 'type_id',
            'value'     => $model->getId()
        ));

        $fieldset->addField('form_type_data', 'hidden', array(
            'name'      => 'form_type_data'
        ));

        $fieldset->addField('code', 'text', array(
            'name'      => 'code',
            'label'     => Mage::helper('Enterprise_Customer_Helper_Data')->__('Form Code'),
            'title'     => Mage::helper('Enterprise_Customer_Helper_Data')->__('Form Code'),
            'required'  => true,
            'class'     => 'validate-code',
            'disabled'  => true,
            'value'     => $model->getCode()
        ));

        $fieldset->addField('label', 'text', array(
            'name'      => 'label',
            'label'     => Mage::helper('Enterprise_Customer_Helper_Data')->__('Form Title'),
            'title'     => Mage::helper('Enterprise_Customer_Helper_Data')->__('Form Title'),
            'required'  => true,
            'value'     => $model->getLabel()
        ));

        $options = Mage::getModel('Mage_Core_Model_Design_Source_Design')->getAllOptions(false, true);
        array_unshift($options, array(
            'label' => Mage::helper('Enterprise_Customer_Helper_Data')->__('All Themes'),
            'value' => ''
        ));
        $fieldset->addField('theme', 'select', array(
            'name'      => 'theme',
            'label'     => Mage::helper('Enterprise_Customer_Helper_Data')->__('For Theme'),
            'title'     => Mage::helper('Enterprise_Customer_Helper_Data')->__('For Theme'),
            'values'    => $options,
            'value'     => $model->getTheme(),
            'disabled'  => true
        ));

        $fieldset->addField('store_id', 'select', array(
            'name'      => 'store_id',
            'label'     => Mage::helper('Enterprise_Customer_Helper_Data')->__('Store View'),
            'title'     => Mage::helper('Enterprise_Customer_Helper_Data')->__('Store View'),
            'values'    => Mage::getSingleton('Mage_Core_Model_System_Store')->getStoreValuesForForm(false, true),
            'value'     => $model->getStoreId(),
            'disabled'  => true
        ));

        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * Retrieve Tab label
     *
     * @return string
     */
    public function getTabLabel()
    {
        return Mage::helper('Enterprise_Customer_Helper_Data')->__('General');
    }

    /**
     * Retrieve Tab title
     *
     * @return string
     */
    public function getTabTitle()
    {
        return Mage::helper('Enterprise_Customer_Helper_Data')->__('General');
    }

    /**
     * Check is can show tab
     *
     * @return bool
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Check tab is hidden
     *
     * @return bool
     */
    public function isHidden()
    {
        return false;
    }
}
