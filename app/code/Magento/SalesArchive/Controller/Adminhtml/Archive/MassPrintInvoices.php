<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
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
