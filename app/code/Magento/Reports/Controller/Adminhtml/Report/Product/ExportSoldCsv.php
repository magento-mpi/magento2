<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Reports\Controller\Adminhtml\Report\Product;

use Magento\Framework\App\Filesystem\DirectoryList;
use \Magento\Framework\App\ResponseInterface;
use \Magento\Backend\Block\Widget\Grid\ExportInterface;

class ExportSoldCsv extends \Magento\Reports\Controller\Adminhtml\Report\Product
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
     * Export Sold Products report to CSV format action
     *
     * @return ResponseInterface
     */
    public function execute()
    {
        $this->_view->loadLayout();
        $fileName = 'products_ordered.csv';
        /** @var ExportInterface $exportBlock */
        $exportBlock = $this->_view->getLayout()->getChildBlock('adminhtml.report.grid', 'grid.export');
        return $this->_fileFactory->create(
            $fileName,
            $exportBlock->getCsvFile(),
            DirectoryList::VAR_DIR
        );
    }
}
