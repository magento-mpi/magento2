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
 * Adminhtml Tax Rule Edit Form
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Magento_Adminhtml_Block_Checkout_Agreement_Edit_Form extends Magento_Adminhtml_Block_Widget_Form
{
    /**
     * Init class
     *
     */
    protected function _construct()
    {
        parent::_construct();

        $this->setId('checkoutAgreementForm');
        $this->setTitle(Mage::helper('Mage_Checkout_Helper_Data')->__('Terms and Conditions Information'));
    }

    /**
     *
     * return Magento_Adminhtml_Block_Widget_Form
     */
    protected function _prepareForm()
    {
        $model  = Mage::registry('checkout_agreement');
        $form   = new Magento_Data_Form(array(
            'id'        => 'edit_form',
            'action'    => $this->getData('action'),
            'method'    => 'post'
        ));

        $fieldset   = $form->addFieldset('base_fieldset', array(
            'legend'    => Mage::helper('Mage_Checkout_Helper_Data')->__('Terms and Conditions Information'),
            'class'     => 'fieldset-wide',
        ));

        if ($model->getId()) {
            $fieldset->addField('agreement_id', 'hidden', array(
                'name' => 'agreement_id',
            ));
        }
        $fieldset->addField('name', 'text', array(
            'name'      => 'name',
            'label'     => Mage::helper('Mage_Checkout_Helper_Data')->__('Condition Name'),
            'title'     => Mage::helper('Mage_Checkout_Helper_Data')->__('Condition Name'),
            'required'  => true,
        ));

        $fieldset->addField('is_active', 'select', array(
            'label'     => Mage::helper('Mage_Checkout_Helper_Data')->__('Status'),
            'title'     => Mage::helper('Mage_Checkout_Helper_Data')->__('Status'),
            'name'      => 'is_active',
            'required'  => true,
            'options'   => array(
                '1' => Mage::helper('Mage_Checkout_Helper_Data')->__('Enabled'),
                '0' => Mage::helper('Mage_Checkout_Helper_Data')->__('Disabled'),
            ),
        ));

        $fieldset->addField('is_html', 'select', array(
            'label'     => Mage::helper('Mage_Checkout_Helper_Data')->__('Show Content as'),
            'title'     => Mage::helper('Mage_Checkout_Helper_Data')->__('Show Content as'),
            'name'      => 'is_html',
            'required'  => true,
            'options'   => array(
                0 => Mage::helper('Mage_Checkout_Helper_Data')->__('Text'),
                1 => Mage::helper('Mage_Checkout_Helper_Data')->__('HTML'),
            ),
        ));

        if (!Mage::app()->isSingleStoreMode()) {
            $field = $fieldset->addField('store_id', 'multiselect', array(
                'name'      => 'stores[]',
                'label'     => Mage::helper('Mage_Checkout_Helper_Data')->__('Store View'),
                'title'     => Mage::helper('Mage_Checkout_Helper_Data')->__('Store View'),
                'required'  => true,
                'values'    => Mage::getSingleton('Mage_Core_Model_System_Store')->getStoreValuesForForm(false, true),
            ));
            $renderer = $this->getLayout()->createBlock('Mage_Backend_Block_Store_Switcher_Form_Renderer_Fieldset_Element');
            $field->setRenderer($renderer);
        }
        else {
            $fieldset->addField('store_id', 'hidden', array(
                'name'      => 'stores[]',
                'value'     => Mage::app()->getStore(true)->getId()
            ));
            $model->setStoreId(Mage::app()->getStore(true)->getId());
        }

        $fieldset->addField('checkbox_text', 'editor', array(
            'name'      => 'checkbox_text',
            'label'     => Mage::helper('Mage_Checkout_Helper_Data')->__('Checkbox Text'),
            'title'     => Mage::helper('Mage_Checkout_Helper_Data')->__('Checkbox Text'),
            'rows'      => '5',
            'cols'      => '30',
            'wysiwyg'   => false,
            'required'  => true,
        ));

        $fieldset->addField('content', 'editor', array(
            'name'      => 'content',
            'label'     => Mage::helper('Mage_Checkout_Helper_Data')->__('Content'),
            'title'     => Mage::helper('Mage_Checkout_Helper_Data')->__('Content'),
            'style'     => 'height:24em;',
            'wysiwyg'   => false,
            'required'  => true,
        ));

        $fieldset->addField('content_height', 'text', array(
            'name'      => 'content_height',
            'label'     => Mage::helper('Mage_Checkout_Helper_Data')->__('Content Height (css)'),
            'title'     => Mage::helper('Mage_Checkout_Helper_Data')->__('Content Height'),
            'maxlength' => 25,
            'class'     => 'validate-css-length',
        ));

        $form->setValues($model->getData());
        $form->setUseContainer(true);
        $this->setForm($form);

        return parent::_prepareForm();
    }
}
