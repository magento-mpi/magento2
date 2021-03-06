<?php
/**
 *
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\SalesArchive\Controller\Adminhtml\Archive;

use Magento\Framework\App\ResponseInterface;
use Magento\Framework\App\Filesystem\DirectoryList;

class ExportCreditmemoCsv extends \Magento\SalesArchive\Controller\Adminhtml\Archive
{
    /**
     * Export credit memo grid archive grid to CSV format
     *
     * @return ResponseInterface
     */
    public function execute()
    {
        $this->_view->loadLayout(false);
        $fileName = 'creditmemo_archive.csv';
        $grid = $this->_view->getLayout()->getChildBlock('sales.creditmemo.grid', 'grid.export');
        $csvFile = $grid->getCsvFile();
        return $this->_fileFactory->create($fileName, $csvFile, DirectoryList::VAR_DIR);
    }
}
