<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Search\Controller\Adminhtml\Term;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\ResponseInterface;

class ExportSearchExcel extends \Magento\Search\Controller\Adminhtml\Term
{
    /**
     * Export search report to Excel XML format
     *
     * @return ResponseInterface
     */
    public function execute()
    {
        $this->_view->loadLayout(false);
        $content = $this->_view->getLayout()->getChildBlock('adminhtml.report.search.grid', 'grid.export');
        return $this->_fileFactory->create('search.xml', $content->getExcelFile(), DirectoryList::VAR_DIR);
    }
}
