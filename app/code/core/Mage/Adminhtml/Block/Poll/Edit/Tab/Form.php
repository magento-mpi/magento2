<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Poll edit form
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Mage_Adminhtml_Block_Poll_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();

        $fieldset = $form->addFieldset('poll_form', array('legend'=>Mage::helper('Mage_Poll_Helper_Data')->__('Poll information')));
        $fieldset->addField('poll_title', 'text', array(
            'label'     => Mage::helper('Mage_Poll_Helper_Data')->__('Poll Question'),
            'class'     => 'required-entry',
            'required'  => true,
            'name'      => 'poll_title',
        ));

        $fieldset->addField('closed', 'select', array(
            'label'     => Mage::helper('Mage_Poll_Helper_Data')->__('Status'),
            'name'      => 'closed',
            'values'    => array(
                array(
                    'value'     => 1,
                    'label'     => Mage::helper('Mage_Poll_Helper_Data')->__('Closed'),
                ),

                array(
                    'value'     => 0,
                    'label'     => Mage::helper('Mage_Poll_Helper_Data')->__('Open'),
                ),
            ),
        ));

        if (!Mage::app()->isSingleStoreMode()) {
            $fieldset->addField('store_ids', 'multiselect', array(
                'label'     => Mage::helper('Mage_Poll_Helper_Data')->__('Visible In'),
                'required'  => true,
                'name'      => 'store_ids[]',
                'values'    => Mage::getSingleton('Mage_Adminhtml_Model_System_Store')->getStoreValuesForForm(),
                'value'     => Mage::registry('poll_data')->getStoreIds(),
                'after_element_html' => Mage::getBlockSingleton('adminhtml/store_switcher')->getHintHtml()
            ));
        }
        else {
            $fieldset->addField('store_ids', 'hidden', array(
                'name'      => 'store_ids[]',
                'value'     => Mage::app()->getStore(true)->getId()
            ));
            Mage::registry('poll_data')->setStoreIds(Mage::app()->getStore(true)->getId());
        }


        if( Mage::getSingleton('Mage_Adminhtml_Model_Session')->getPollData() ) {
            $form->setValues(Mage::getSingleton('Mage_Adminhtml_Model_Session')->getPollData());
            Mage::getSingleton('Mage_Adminhtml_Model_Session')->setPollData(null);
        } elseif( Mage::registry('poll_data') ) {
            $form->setValues(Mage::registry('poll_data')->getData());

            $fieldset->addField('was_closed', 'hidden', array(
                'name'      => 'was_closed',
                'no_span'   => true,
                'value'     => Mage::registry('poll_data')->getClosed()
            ));
        }

        $this->setForm($form);
        return parent::_prepareForm();
    }
}
