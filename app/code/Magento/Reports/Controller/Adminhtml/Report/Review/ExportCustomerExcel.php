<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Reports\Controller\Adminhtml\Report\Review;

use \Magento\Framework\App\ResponseInterface;

class ExportCustomerExcel extends \Magento\Reports\Controller\Adminhtml\Report\Review
{
    /**
     * Export review customer report to Excel XML format
     *
     * @return ResponseInterface
     */
    public function execute()
    {
        $this->_view->loadLayout(false);
        $fileName = 'review_customer.xml';
        $exportBlock = $this->_view->getLayout()->getChildBlock(
            'adminhtml.block.report.review.customer.grid',
            'grid.export'
        );
        return $this->_fileFactory->create($fileName, $exportBlock->getExcelFile(), \Magento\Framework\App\Filesystem::VAR_DIR);
    }
}
