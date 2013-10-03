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

namespace Magento\Adminhtml\Block\Sales\Order\Create;

class Customer extends \Magento\Adminhtml\Block\Sales\Order\Create\AbstractCreate
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
        return $this->getLayout()->createBlock('Magento\Adminhtml\Block\Widget\Button')->setData($addButtonData)->toHtml();
    }

}
