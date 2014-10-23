<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\SalesArchive\Controller\Adminhtml\Archive;

use Magento\Framework\App\Filesystem\DirectoryList;
use \Magento\Framework\App\ResponseInterface;

class ExportInvoiceCsv extends \Magento\SalesArchive\Controller\Adminhtml\Archive
{
    /**
     * Export archive invoice to CSV format
     *
     * @return ResponseInterface
     */
    public function execute()
    {
        $this->_view->loadLayout(false);
        $fileName = 'invoice_archive.csv';
        $grid = $this->_view->getLayout()->getChildBlock('sales.invoice.grid', 'grid.export');
        $csvFile = $grid->getCsvFile();
        return $this->_fileFactory->create($fileName, $csvFile, DirectoryList::VAR_DIR);
    }
}
