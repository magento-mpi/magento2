<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_SalesArchive
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Archive invoice block
 *
 */

namespace Magento\SalesArchive\Block\Adminhtml\Sales\Archive\Order;

class Invoice extends \Magento\SalesArchive\Block\Adminhtml\Sales\Archive\Order\Container
{
    protected function _construct()
    {
        $this->_controller = 'sales_invoice';
        $this->_headerText = __('Invoices Archive');
    }
}
