<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
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
        $this->_title->add(__('Invoices'));

        $this->_view->loadLayout();
        $this->_setActiveMenu('Magento_SalesArchive::sales_archive_invoices');
        $this->_view->renderLayout();
    }
}
