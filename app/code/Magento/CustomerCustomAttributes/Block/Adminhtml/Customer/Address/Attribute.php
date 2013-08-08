<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CustomerCustomAttributes
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Customer address attributes Grid Container
 *
 * @category    Magento
 * @package     Magento_CustomerCustomAttributes
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_CustomerCustomAttributes_Block_Adminhtml_Customer_Address_Attribute
    extends Magento_Adminhtml_Block_Widget_Grid_Container
{
    /**
     * Define controller, block and labels
     *
     */
    protected function _construct()
    {
        $this->_blockGroup = 'Magento_CustomerCustomAttributes';
        $this->_controller = 'adminhtml_customer_address_attribute';
        $this->_headerText = Mage::helper('Magento_CustomerCustomAttributes_Helper_Data')->__('Customer Address Attributes');
        $this->_addButtonLabel = Mage::helper('Magento_CustomerCustomAttributes_Helper_Data')->__('Add New Attribute');
        parent::_construct();
    }
}
