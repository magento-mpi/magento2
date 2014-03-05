<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_SalesArchive
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\SalesArchive\Block\Adminhtml\Sales\Archive\Order;

/**
 * Archive shipment block
 */
class Shipment extends \Magento\SalesArchive\Block\Adminhtml\Sales\Archive\Order\Container
{
    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_controller = 'sales_shipment';
        $this->_headerText = __('Shipments Archive');
    }
}
