<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\SalesArchive\Controller\Adminhtml\Archive;

class Shipments extends \Magento\SalesArchive\Controller\Adminhtml\Archive
{
    /**
     * Shipments view page
     *
     * @return void
     */
    public function execute()
    {
        $this->_view->loadLayout();
        $this->_setActiveMenu('Magento_SalesArchive::sales_archive_shipments');
        $this->_view->getPage()->getConfig()->getTitle()->prepend(__('Shipments'));
        $this->_view->renderLayout();
    }
}
