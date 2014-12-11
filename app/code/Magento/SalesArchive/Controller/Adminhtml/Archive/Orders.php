<?php
/**
 *
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
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
        $this->_view->loadLayout();
        $this->_setActiveMenu('Magento_SalesArchive::sales_archive_orders');
        $this->_view->getPage()->getConfig()->getTitle()->prepend(__('Orders'));
        $this->_view->renderLayout();
    }
}
