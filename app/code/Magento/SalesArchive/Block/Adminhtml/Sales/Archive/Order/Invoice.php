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
 * Archive invoice block
 */
class Invoice extends \Magento\SalesArchive\Block\Adminhtml\Sales\Archive\Order\Container
{
    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_controller = 'sales_invoice';
        $this->_headerText = __('Invoices Archive');
    }
}
