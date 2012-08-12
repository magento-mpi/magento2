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
 * Adminhtml Tax Class Edit
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Mage_Adminhtml_Block_Tax_Class_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    protected function _construct()
    {
        $this->_objectId    = 'id';
        $this->_controller  = 'tax_class';

        parent::_construct();

        $this->_updateButton('save', 'label', Mage::helper('Mage_Tax_Helper_Data')->__('Save Class'));
        $this->_updateButton('delete', 'label', Mage::helper('Mage_Tax_Helper_Data')->__('Delete Class'));
    }

    public function getHeaderText()
    {
        if (Mage::registry('tax_class')->getId()) {
            return Mage::helper('Mage_Tax_Helper_Data')->__("Edit Class '%s'", $this->escapeHtml(Mage::registry('tax_class')->getClassName()));
        }
        else {
            return Mage::helper('Mage_Tax_Helper_Data')->__('New Class');
        }
    }

    public function setClassType($classType)
    {
        $this->getChildBlock('form')->setClassType($classType);
        return $this;
    }
}
