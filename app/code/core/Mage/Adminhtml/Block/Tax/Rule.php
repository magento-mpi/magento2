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
 * Admin tax rule content block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Mage_Adminhtml_Block_Tax_Rule extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    protected function _construct()
    {
        $this->_controller      = 'tax_rule';
        $this->_headerText      = Mage::helper('Mage_Tax_Helper_Data')->__('Manage Tax Rules');
        $this->_addButtonLabel  = Mage::helper('Mage_Tax_Helper_Data')->__('Add New Tax Rule');
        parent::_construct();
    }
}
