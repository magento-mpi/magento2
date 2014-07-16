<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Tax\Controller\Adminhtml\Rate;

use \Magento\Framework\App\ResponseInterface;

class ExportCsv extends \Magento\Tax\Controller\Adminhtml\Rate
{
    /**
     * Export rates grid to CSV format
     *
     * @return ResponseInterface
     */
    public function execute()
    {
        $this->_view->loadLayout(false);
        $content = $this->_view->getLayout()->getChildBlock('adminhtml.tax.rate.grid', 'grid.export');
        return $this->_fileFactory->create(
            'rates.csv',
            $content->getCsvFile(),
            \Magento\Framework\App\Filesystem::VAR_DIR
        );
    }
}
