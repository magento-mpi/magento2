<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Reports\Controller\Adminhtml\Index;

use \Magento\Framework\App\ResponseInterface;

class ExportSearchCsv extends \Magento\Reports\Controller\Adminhtml\Index
{
    /**
     * Export search report grid to CSV format
     *
     * @return ResponseInterface
     */
    public function execute()
    {
        $this->_view->loadLayout(false);
        $content = $this->_view->getLayout()->getChildBlock('adminhtml.report.search.grid', 'grid.export');
        return $this->_fileFactory->create('search.csv', $content->getCsvFile(), \Magento\Framework\App\Filesystem::VAR_DIR);
    }
}
