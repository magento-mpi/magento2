<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Controller\Adminhtml;

use Magento\Framework\App\ResponseInterface;

/**
 * Adminhtml sales invoices controller
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Invoice extends \Magento\Sales\Controller\Adminhtml\Invoice\AbstractInvoice
{
    /**
     * Export invoice grid to CSV format
     *
     * @return ResponseInterface
     */
    public function exportCsvAction()
    {
        $this->_view->loadLayout();
        $fileName = 'invoices.csv';
        /** @var \Magento\Backend\Block\Widget\Grid\ExportInterface $exportBlock  */
        $exportBlock = $this->_view->getLayout()->getChildBlock('sales.invoice.grid', 'grid.export');
        return $this->_fileFactory->create(
            $fileName, 
            $exportBlock->getCsvFile(), 
            \Magento\Framework\App\Filesystem::VAR_DIR
        );
    }

    /**
     * Export invoice grid to Excel XML format
     *
     * @return ResponseInterface
     */
    public function exportExcelAction()
    {
         $this->_view->loadLayout();
        $fileName = 'invoices.xml';
        $exportBlock = $this->_view->getLayout()->getChildBlock('sales.invoice.grid', 'grid.export');
        return $this->_fileFactory->create(
            $fileName,
            $exportBlock->getExcelFile($fileName),
            \Magento\Framework\App\Filesystem::VAR_DIR
        );
    }
}
