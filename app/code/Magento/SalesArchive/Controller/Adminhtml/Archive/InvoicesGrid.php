<?php
/**
 *
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\SalesArchive\Controller\Adminhtml\Archive;

class InvoicesGrid extends \Magento\SalesArchive\Controller\Adminhtml\Archive
{
    /**
     * Invoices grid
     *
     * @return void
     */
    public function execute()
    {
        $this->_renderGrid();
    }
}
