<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Customer
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Customer address attributes Grid Container
 *
 * @category    Enterprise
 * @package     Enterprise_Customer
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Customer_Block_Adminhtml_Customer_Address_Attribute
    extends Magento_Adminhtml_Block_Widget_Grid_Container
{
    /**
     * Define controller, block and labels
     *
     */
    protected function _construct()
    {
        $this->_blockGroup = 'Enterprise_Customer';
        $this->_controller = 'adminhtml_customer_address_attribute';
        $this->_headerText = __('Customer Address Attributes');
        $this->_addButtonLabel = __('Add New Attribute');
        parent::_construct();
    }
}
