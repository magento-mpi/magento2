<?php
/**
 *
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\SalesArchive\Controller\Adminhtml\Archive;

class Creditmemos extends \Magento\SalesArchive\Controller\Adminhtml\Archive
{
    /**
     * Creditmemos view page
     *
     * @return void
     */
    public function execute()
    {
        $this->_view->loadLayout();
        $this->_setActiveMenu('Magento_SalesArchive::sales_archive_creditmemos');
        $this->_view->getPage()->getConfig()->getTitle()->prepend(__('Credit Memos'));
        $this->_view->renderLayout();
    }
}
