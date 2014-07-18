<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
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
