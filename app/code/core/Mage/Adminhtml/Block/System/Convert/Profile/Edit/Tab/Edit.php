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
 * Convert profile edit tab
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_System_Convert_Profile_Edit_Tab_Edit extends Mage_Adminhtml_Block_Widget_Form
{
    public function initForm()
    {
        $form = new Varien_Data_Form();
        $form->setHtmlIdPrefix('_edit');

        $model = Mage::registry('current_convert_profile');

        $fieldset = $form->addFieldset('base_fieldset', array(
            'legend'=>Mage::helper('Mage_Adminhtml_Helper_Data')->__('General Information'),
            'class'=>'fieldset-wide'
        ));

        $fieldset->addField('name', 'text', array(
            'name' => 'name',
            'label' => Mage::helper('Mage_Adminhtml_Helper_Data')->__('Profile Name'),
            'title' => Mage::helper('Mage_Adminhtml_Helper_Data')->__('Profile Name'),
            'required' => true,
        ));

        $fieldset->addField('actions_xml', 'textarea', array(
            'name' => 'actions_xml',
            'label' => Mage::helper('Mage_Adminhtml_Helper_Data')->__('Actions XML'),
            'title' => Mage::helper('Mage_Adminhtml_Helper_Data')->__('Actions XML'),
            'style' => 'height:30em',
            'required' => true,
        ));


        $form->setValues($model->getData());

        $this->setForm($form);

        return $this;
    }
}
