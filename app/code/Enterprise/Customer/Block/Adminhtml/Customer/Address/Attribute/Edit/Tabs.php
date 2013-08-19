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
 * Customer Address Attribute Tab Block
 *
 * @category    Enterprise
 * @package     Enterprise_Customer
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Customer_Block_Adminhtml_Customer_Address_Attribute_Edit_Tabs
    extends Magento_Adminhtml_Block_Widget_Tabs
{
    /**
     * Initialize edit tabs
     *
     */
    protected function _construct()
    {
        parent::_construct();

        $this->setId('enterprise_customer_address_attribute_edit_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Attribute Information'));
    }
}
