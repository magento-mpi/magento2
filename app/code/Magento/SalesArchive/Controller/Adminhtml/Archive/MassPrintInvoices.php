<?php
/**
 *
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\SalesArchive\Controller\Adminhtml\Archive;

class MassPrintInvoices extends \Magento\SalesArchive\Controller\Adminhtml\Archive
{
    /**
     * Print invoices mass action
     *
     * @return void
     */
    public function execute()
    {
        $this->_forward('pdfinvoices', 'order', null, ['origin' => 'archive']);
    }
}
