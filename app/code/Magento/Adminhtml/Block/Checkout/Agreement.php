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

class Magento_Adminhtml_Block_Checkout_Agreement extends Magento_Adminhtml_Block_Widget_Grid_Container
{
    protected function _construct()
    {
        $this->_controller      = 'checkout_agreement';
        $this->_headerText      = Mage::helper('Magento_Checkout_Helper_Data')->__('Terms and Conditions');
        $this->_addButtonLabel  = Mage::helper('Magento_Checkout_Helper_Data')->__('Add New Condition');
        parent::_construct();
    }
}
