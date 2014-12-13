<?php
/**
 *
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\SalesArchive\Controller\Adminhtml\Archive;

class Invoices extends \Magento\SalesArchive\Controller\Adminhtml\Archive
{
    /**
     * Invoices view page
     *
     * @return void
     */
    public function execute()
    {
        $this->_view->loadLayout();
        $this->_setActiveMenu('Magento_SalesArchive::sales_archive_invoices');
        $this->_view->getPage()->getConfig()->getTitle()->prepend(__('Invoices'));
        $this->_view->renderLayout();
    }
}
