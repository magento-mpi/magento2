<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Controller\Adminhtml\Order;


class Invoices extends \Magento\Sales\Controller\Adminhtml\Order
{
    /**
     * Generate invoices grid for ajax request
     *
     * @return void
     */
    public function execute()
    {
        $this->_initOrder();
        $this->_view->loadLayout(false);
        $this->_view->renderLayout();
    }
}
