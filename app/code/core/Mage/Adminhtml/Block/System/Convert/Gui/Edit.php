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
 * Convert profile edit block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_System_Convert_Gui_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    protected function _construct()
    {
        $this->_objectId = 'id';
        $this->_controller = 'system_convert_gui';

        parent::_construct();

        $this->_updateButton('save', 'label', Mage::helper('Mage_Adminhtml_Helper_Data')->__('Save Profile'));
        $this->_updateButton('delete', 'label', Mage::helper('Mage_Adminhtml_Helper_Data')->__('Delete Profile'));
        $this->_addButton('savecontinue', array(
            'label' => Mage::helper('Mage_Adminhtml_Helper_Data')->__('Save and Continue Edit'),
            'onclick' => "$('edit_form').action += 'continue/true/'; editForm.submit();",
            'class' => 'save',
        ), -100);

    }

    public function getProfileId()
    {
        return Mage::registry('current_convert_profile')->getId();
    }

    public function getHeaderText()
    {
        if (Mage::registry('current_convert_profile')->getId()) {
            return $this->escapeHtml(Mage::registry('current_convert_profile')->getName());
        }
        else {
            return Mage::helper('Mage_Adminhtml_Helper_Data')->__('New Profile');
        }
    }
}
