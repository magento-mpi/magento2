<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Reports\Controller\Adminhtml\Report\Sales;

use \Magento\Framework\App\ResponseInterface;

class ExportCouponsCsv extends \Magento\Reports\Controller\Adminhtml\Report\Sales
{
    /**
     * Export coupons report grid to CSV format
     *
     * @return ResponseInterface
     */
    public function execute()
    {
        $fileName = 'coupons.csv';
        $grid = $this->_view->getLayout()->createBlock('Magento\Reports\Block\Adminhtml\Sales\Coupons\Grid');
        $this->_initReportAction($grid);
        return $this->_fileFactory->create($fileName, $grid->getCsvFile(), \Magento\Framework\App\Filesystem::VAR_DIR);
    }
}
