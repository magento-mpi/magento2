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

class ExportProductDetailCsv extends \Magento\Reports\Controller\Adminhtml\Report\Review
{
    /**
     * Export review product detail report to CSV format
     *
     * @return ResponseInterface
     */
    public function execute()
    {
        $fileName = 'review_product_detail.csv';
        $content = $this->_view->getLayout()->createBlock(
            'Magento\Reports\Block\Adminhtml\Review\Detail\Grid'
        )->getCsv();

        return $this->_fileFactory->create($fileName, $content, DirectoryList::VAR_DIR);
    }
}
