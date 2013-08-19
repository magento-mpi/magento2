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
 * Adminhtml sales order create block
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Magento_Adminhtml_Block_Sales_Order_Create_Customer extends Magento_Adminhtml_Block_Sales_Order_Create_Abstract
{

    protected function _construct()
    {
        parent::_construct();
        $this->setId('sales_order_create_customer');
    }

    public function getHeaderText()
    {
        return __('Please select a customer.');
    }

    public function getButtonsHtml()
    {
        $addButtonData = array(
            'label'     => __('Create New Customer'),
            'onclick'   => 'order.setCustomerId(false)',
            'class'     => 'primary',
        );
        return $this->getLayout()->createBlock('Magento_Adminhtml_Block_Widget_Button')->setData($addButtonData)->toHtml();
    }

}
