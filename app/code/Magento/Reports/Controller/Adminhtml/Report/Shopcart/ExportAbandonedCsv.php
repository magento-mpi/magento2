<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Reports\Controller\Adminhtml\Report\Shopcart;

use \Magento\Framework\App\ResponseInterface;

class ExportAbandonedCsv extends \Magento\Reports\Controller\Adminhtml\Report\Shopcart
{
    /**
     * Export abandoned carts report grid to CSV format
     *
     * @return ResponseInterface
     */
    public function execute()
    {
        $fileName = 'shopcart_abandoned.csv';
        $content = $this->_view->getLayout()->createBlock(
            'Magento\Reports\Block\Adminhtml\Shopcart\Abandoned\Grid'
        )->getCsvFile();

        return $this->_fileFactory->create($fileName, $content, \Magento\Framework\App\Filesystem::VAR_DIR);
    }
}
