<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\SalesArchive\Controller\Adminhtml\Archive;

class Remove extends \Magento\SalesArchive\Controller\Adminhtml\Archive
{
    /**
     * Unarchive order action
     *
     * @return void
     */
    public function execute()
    {
        $orderId = $this->getRequest()->getParam('order_id');
        if ($orderId) {
            $this->_archiveModel->removeOrdersFromArchiveById($orderId);
            $this->messageManager->addSuccess(__('We have removed the order from the archive.'));
            $this->_redirect('sales/order/view', array('order_id' => $orderId));
        } else {
            $this->messageManager->addError(__('Please specify the order ID to be removed from archive.'));
            $this->_redirect('sales/order');
        }
    }
}
