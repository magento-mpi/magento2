<?php
/**
 *
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\SalesArchive\Controller\Adminhtml\Archive;

class MassAdd extends \Magento\SalesArchive\Controller\Adminhtml\Archive
{
    /**
     * Massaction for adding orders to archive
     *
     * @return void
     */
    public function execute()
    {
        $orderIds = $this->getRequest()->getPost('order_ids', []);
        $archivedIds = $this->_archiveModel->archiveOrdersById($orderIds);

        $archivedCount = count($archivedIds);
        if ($archivedCount > 0) {
            $this->messageManager->addSuccess(__('We archived %1 order(s).', $archivedCount));
        } else {
            $this->messageManager->addWarning(__("We can't archive the selected order(s)."));
        }
        $this->_redirect('sales/order/');
    }
}
