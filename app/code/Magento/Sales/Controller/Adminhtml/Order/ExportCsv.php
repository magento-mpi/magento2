<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Controller\Adminhtml\Order;

use Magento\Framework\App\Filesystem\DirectoryList;
use \Magento\Framework\App\ResponseInterface;
use \Magento\Backend\App\Action;

class ExportCsv extends \Magento\Sales\Controller\Adminhtml\Order
{
    /**
     * Export order grid to CSV format
     *
     * @return ResponseInterface|void
     */
    public function execute()
    {
        $this->_view->loadLayout();
        $fileName = 'orders.csv';
        /** @var \Magento\Backend\Block\Widget\Grid\ExportInterface $exportBlock  */
        $exportBlock = $this->_view->getLayout()->getChildBlock('sales.order.grid', 'grid.export');
        return $this->_fileFactory->create($fileName, $exportBlock->getCsvFile(), DirectoryList::VAR_DIR);
    }
}
