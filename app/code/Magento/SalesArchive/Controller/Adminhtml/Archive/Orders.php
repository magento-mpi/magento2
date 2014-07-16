<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\SalesArchive\Controller\Adminhtml\Archive;

class Orders extends \Magento\SalesArchive\Controller\Adminhtml\Archive
{
    /**
     * Orders view page
     *
     * @return void
     */
    public function execute()
    {
        $this->_title->add(__('Orders'));

        $this->_view->loadLayout();
        $this->_setActiveMenu('Magento_SalesArchive::sales_archive_orders');
        $this->_view->renderLayout();
    }
}
