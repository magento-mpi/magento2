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
 * Admin tax class content block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Mage_Adminhtml_Block_Tax_Class extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    protected function _construct()
    {
        $this->_controller      = 'tax_class';
        parent::_construct();
    }

    public function setClassType($classType)
    {
        if ($classType == Mage_Tax_Model_Class::TAX_CLASS_TYPE_PRODUCT) {
            $this->_headerText      = Mage::helper('Mage_Tax_Helper_Data')->__('Product Tax Classes');
            $this->_addButtonLabel  = Mage::helper('Mage_Tax_Helper_Data')->__('Add New Class');
        }
        elseif ($classType == Mage_Tax_Model_Class::TAX_CLASS_TYPE_CUSTOMER) {
            $this->_headerText      = Mage::helper('Mage_Tax_Helper_Data')->__('Customer Tax Classes');
            $this->_addButtonLabel  = Mage::helper('Mage_Tax_Helper_Data')->__('Add New Class');
        }

        $this->getChildBlock('grid')->setClassType($classType);
        $this->setData('class_type', $classType);

        return $this;
    }
}
