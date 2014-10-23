<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Reports\Controller\Adminhtml\Report\Review;

use Magento\Framework\App\Filesystem\DirectoryList;
use \Magento\Framework\App\ResponseInterface;

class ExportProductCsv extends \Magento\Reports\Controller\Adminhtml\Report\Review
{
    /**
     * Export review product report to CSV format
     *
     * @return ResponseInterface
     */
    public function execute()
    {
        $this->_view->loadLayout(false);
        $fileName = 'review_product.csv';
        $exportBlock = $this->_view->getLayout()->getChildBlock(
            'adminhtml.block.report.review.product.grid',
            'grid.export'
        );
        return $this->_fileFactory->create($fileName, $exportBlock->getCsvFile(), DirectoryList::VAR_DIR);
    }
}
