<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Reports\Controller\Adminhtml\Report\Product;

use \Magento\Framework\App\ResponseInterface;

class ExportViewedExcel extends \Magento\Reports\Controller\Adminhtml\Report\Product
{
    /**
     * Check is allowed for report
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magento_Reports::report_products');
    }

    /**
     * Export products most viewed report to XML format
     *
     * @return ResponseInterface
     */
    public function execute()
    {
        $fileName = 'products_mostviewed.xml';
        $grid = $this->_view->getLayout()->createBlock('Magento\Reports\Block\Adminhtml\Product\Viewed\Grid');
        $this->_initReportAction($grid);
        return $this->_fileFactory->create(
            $fileName,
            $grid->getExcelFile($fileName),
            \Magento\Framework\App\Filesystem::VAR_DIR
        );
    }
}
