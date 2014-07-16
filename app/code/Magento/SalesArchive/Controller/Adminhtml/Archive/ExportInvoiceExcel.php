<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\SalesArchive\Controller\Adminhtml\Archive;

use \Magento\Framework\App\ResponseInterface;

class ExportInvoiceExcel extends \Magento\SalesArchive\Controller\Adminhtml\Archive
{
    /**
     * Export archive invoice grid to Excel XML format
     *
     * @return ResponseInterface
     */
    public function execute()
    {
        $this->_view->loadLayout(false);
        $fileName = 'invoice_archive.xml';
        $exportBlock = $this->_view->getLayout()->getChildBlock('sales.invoice.grid', 'grid.export');
        return $this->_fileFactory->create(
            $fileName,
            $exportBlock->getExcelFile($fileName),
            \Magento\Framework\App\Filesystem::VAR_DIR
        );
    }
}
