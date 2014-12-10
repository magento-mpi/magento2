<?php
/**
 *
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\SalesArchive\Controller\Adminhtml\Archive;

class MassRemove extends \Magento\SalesArchive\Controller\Adminhtml\Archive
{
    /**
     * Massaction for removing orders from archive
     *
     * @return void
     */
    public function execute()
    {
        $orderIds = $this->getRequest()->getPost('order_ids', []);
        $removedFromArchive = $this->_archiveModel->removeOrdersFromArchiveById($orderIds);

        $removedFromArchiveCount = count($removedFromArchive);
        if ($removedFromArchiveCount > 0) {
            $this->messageManager->addSuccess(
                __('We removed %1 order(s) from the archive.', $removedFromArchiveCount)
            );
        } else {
            // selected orders is not available for removing from archive
        }
        $this->_redirect('sales/archive/orders');
    }
}
