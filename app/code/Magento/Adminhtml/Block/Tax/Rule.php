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
 * Admin tax rule content block
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Magento_Adminhtml_Block_Tax_Rule extends Magento_Adminhtml_Block_Widget_Grid_Container
{
    protected function _construct()
    {
        $this->_controller      = 'tax_rule';
        $this->_headerText      = Mage::helper('Magento_Tax_Helper_Data')->__('Manage Tax Rules');
        $this->_addButtonLabel  = Mage::helper('Magento_Tax_Helper_Data')->__('Add New Tax Rule');
        parent::_construct();
    }
}
