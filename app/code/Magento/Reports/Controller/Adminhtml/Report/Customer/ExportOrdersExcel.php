<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Reports\Controller\Adminhtml\Report\Customer;

use Magento\Framework\App\Filesystem\DirectoryList;
use \Magento\Framework\App\ResponseInterface;
use \Magento\Backend\Block\Widget\Grid\ExportInterface;

class ExportOrdersExcel extends \Magento\Reports\Controller\Adminhtml\Report\Customer
{
    /**
     * Export customers most ordered report to Excel XML format
     *
     * @return ResponseInterface
     */
    public function execute()
    {
        $this->_view->loadLayout();
        $fileName = 'customers_orders.xml';
        /** @var ExportInterface $exportBlock */
        $exportBlock = $this->_view->getLayout()->getChildBlock('adminhtml.report.grid', 'grid.export');
        return $this->_fileFactory->create(
            $fileName,
            $exportBlock->getExcelFile($fileName),
            DirectoryList::VAR_DIR
        );
    }
}
