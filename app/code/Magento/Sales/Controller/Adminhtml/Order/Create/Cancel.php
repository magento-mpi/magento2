<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Controller\Adminhtml\Order\Create;

use \Magento\Backend\App\Action;

class Cancel extends \Magento\Sales\Controller\Adminhtml\Order\Create
{
    /**
     * Cancel order create
     *
     * @return void
     */
    public function execute()
    {
        if ($orderId = $this->_getSession()->getReordered()) {
            $this->_getSession()->clearStorage();
            $this->_redirect('sales/order/view', array('order_id' => $orderId));
        } else {
            $this->_getSession()->clearStorage();
            $this->_redirect('sales/*');
        }
    }
}
