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

class ExportLowstockCsv extends \Magento\Reports\Controller\Adminhtml\Report\Product
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
     * Export low stock products report to CSV format
     *
     * @return ResponseInterface
     */
    public function execute()
    {
        $this->_view->loadLayout(false);
        $fileName = 'products_lowstock.csv';
        $exportBlock = $this->_view->getLayout()->getChildBlock(
            'adminhtml.block.report.product.lowstock.grid',
            'grid.export'
        );
        return $this->_fileFactory->create(
            $fileName,
            $exportBlock->getCsvFile(),
            \Magento\Framework\App\Filesystem::VAR_DIR
        );
    }
}
