<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\SalesArchive\Controller\Adminhtml\Archive;

use Magento\Framework\App\Filesystem\DirectoryList;
use \Magento\Framework\App\ResponseInterface;

class ExportCreditmemoExcel extends \Magento\SalesArchive\Controller\Adminhtml\Archive
{
    /**
     * Export credit memo grid  archive grid to Excel XML format
     *
     * @return ResponseInterface
     */
    public function execute()
    {
        $this->_view->loadLayout(false);
        $fileName = 'creditmemo_archive.xml';
        $grid = $this->_view->getLayout()->getChildBlock('sales.creditmemo.grid', 'grid.export');
        return $this->_fileFactory->create(
            $fileName,
            $grid->getExcelFile($fileName),
            DirectoryList::VAR_DIR
        );
    }
}
