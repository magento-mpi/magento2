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

class ExportCustomerCsv extends \Magento\Reports\Controller\Adminhtml\Report\Review
{
    /**
     * Export review customer report to CSV format
     *
     * @return ResponseInterface
     */
    public function execute()
    {
        $this->_view->loadLayout(false);
        $fileName = 'review_customer.csv';
        $exportBlock = $this->_view->getLayout()->getChildBlock(
            'adminhtml.block.report.review.customer.grid',
            'grid.export'
        );
        return $this->_fileFactory->create($fileName, $exportBlock->getCsvFile(), \Magento\Framework\App\Filesystem::VAR_DIR);
    }
}
