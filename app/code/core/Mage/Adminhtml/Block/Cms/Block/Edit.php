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
 * CMS block edit form container
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Cms_Block_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    protected function _construct()
    {
        $this->_objectId = 'block_id';
        $this->_controller = 'cms_block';

        parent::_construct();

        $this->_updateButton('save', 'label', Mage::helper('Mage_Cms_Helper_Data')->__('Save Block'));
        $this->_updateButton('delete', 'label', Mage::helper('Mage_Cms_Helper_Data')->__('Delete Block'));

        $this->_addButton('saveandcontinue', array(
            'label'     => Mage::helper('Mage_Adminhtml_Helper_Data')->__('Save and Continue Edit'),
            'class'     => 'save',
            'data_attribute'  => array(
                'mage-init' => array(
                    'button' => array('event' => 'saveAndContinueEdit', 'target' => '#edit_form'),
                ),
            ),
        ), -100);

        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('block_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'block_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'block_content');
                }
            }
        ";
    }

    /**
     * Get edit form container header text
     *
     * @return string
     */
    public function getHeaderText()
    {
        if (Mage::registry('cms_block')->getId()) {
            return Mage::helper('Mage_Cms_Helper_Data')->__("Edit Block '%s'", $this->escapeHtml(Mage::registry('cms_block')->getTitle()));
        }
        else {
            return Mage::helper('Mage_Cms_Helper_Data')->__('New Block');
        }
    }

}
