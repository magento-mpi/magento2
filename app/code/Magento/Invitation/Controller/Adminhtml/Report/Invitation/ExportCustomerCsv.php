<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Invitation\Controller\Adminhtml\Report\Invitation;

use Magento\Framework\App\ResponseInterface;

class ExportCustomerCsv extends \Magento\Invitation\Controller\Adminhtml\Report\Invitation
{
    /**
     * Export invitation customer report grid to CSV format
     *
     * @return ResponseInterface
     */
    public function execute()
    {
        $this->_view->loadLayout();
        $fileName = 'invitation_customer.csv';
        /** @var \Magento\Backend\Block\Widget\Grid\ExportInterface $exportBlock */
        $exportBlock = $this->_view->getLayout()->getChildBlock('adminhtml.report.grid', 'grid.export');
        return $this->_fileFactory->create(
            $fileName,
            $exportBlock->getCsvFile(),
            \Magento\Framework\App\Filesystem::VAR_DIR
        );
    }
}
