<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Reports\Controller\Adminhtml\Report\Sales;

use \Magento\Framework\App\ResponseInterface;

class ExportInvoicedExcel extends \Magento\Reports\Controller\Adminhtml\Report\Sales
{
    /**
     * Export invoiced report grid to Excel XML format
     *
     * @return ResponseInterface
     */
    public function execute()
    {
        $fileName = 'invoiced.xml';
        $grid = $this->_view->getLayout()->createBlock('Magento\Reports\Block\Adminhtml\Sales\Invoiced\Grid');
        $this->_initReportAction($grid);
        return $this->_fileFactory->create($fileName, $grid->getExcelFile($fileName), \Magento\Framework\App\Filesystem::VAR_DIR);
    }
}
