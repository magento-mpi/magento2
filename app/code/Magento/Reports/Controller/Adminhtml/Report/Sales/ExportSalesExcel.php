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

class ExportSalesExcel extends \Magento\Reports\Controller\Adminhtml\Report\Sales
{
    /**
     * Export sales report grid to Excel XML format
     *
     * @return ResponseInterface
     */
    public function execute()
    {
        $fileName = 'sales.xml';
        $grid = $this->_view->getLayout()->createBlock('Magento\Reports\Block\Adminhtml\Sales\Sales\Grid');
        $this->_initReportAction($grid);
        return $this->_fileFactory->create($fileName, $grid->getExcelFile($fileName), \Magento\Framework\App\Filesystem::VAR_DIR);
    }
}
