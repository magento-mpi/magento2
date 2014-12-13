<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
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
