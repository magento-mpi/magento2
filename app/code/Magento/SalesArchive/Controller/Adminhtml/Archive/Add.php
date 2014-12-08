<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\SalesArchive\Controller\Adminhtml\Archive;

class Add extends \Magento\SalesArchive\Controller\Adminhtml\Archive
{
    /**
     * Archive order action
     *
     * @return void
     */
    public function execute()
    {
        $orderId = $this->getRequest()->getParam('order_id');
        if ($orderId) {
            $this->_archiveModel->archiveOrdersById($orderId);
            $this->messageManager->addSuccess(__('We have archived the order.'));
            $this->_redirect('sales/order/view', ['order_id' => $orderId]);
        } else {
            $this->messageManager->addError(__('Please specify the order ID to be archived.'));
            $this->_redirect('sales/order');
        }
    }
}
