<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml sales order create block
 *
 * @category   Magento
 * @package    Magento_Sales
 * @author      Magento Core Team <core@magentocommerce.com>
 */

namespace Magento\Sales\Block\Adminhtml\Order\Create;

class Customer extends \Magento\Sales\Block\Adminhtml\Order\Create\AbstractCreate
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
        return $this->getLayout()->createBlock('Magento\Backend\Block\Widget\Button')->setData($addButtonData)->toHtml();
    }

}
