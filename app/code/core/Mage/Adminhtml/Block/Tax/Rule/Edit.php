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
 * Adminhtml tax rule Edit Container
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Mage_Adminhtml_Block_Tax_Rule_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    /**
     * Init class
     *
     */
    protected function _construct()
    {
        $this->_objectId = 'rule';
        $this->_controller = 'tax_rule';

        parent::_construct();

        $this->_updateButton('save', 'label', Mage::helper('Mage_Tax_Helper_Data')->__('Save Rule'));
        $this->_updateButton('delete', 'label', Mage::helper('Mage_Tax_Helper_Data')->__('Delete Rule'));

        $this->_addButton('save_and_continue', array(
            'label'     => Mage::helper('Mage_Tax_Helper_Data')->__('Save and Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class' => 'save'
        ), 10);

        $this->_formScripts[] = " function saveAndContinueEdit(){ editForm.submit($('edit_form').action + 'back/edit/') } ";
    }

    /**
     * Get Header text
     *
     * @return string
     */
    public function getHeaderText()
    {
        if (Mage::registry('tax_rule')->getId()) {
            return Mage::helper('Mage_Tax_Helper_Data')->__("Edit Rule");
        }
        else {
            return Mage::helper('Mage_Tax_Helper_Data')->__('New Rule');
        }
    }
}
