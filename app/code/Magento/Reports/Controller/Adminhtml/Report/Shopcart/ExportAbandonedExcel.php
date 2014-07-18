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

class ExportAbandonedExcel extends \Magento\Reports\Controller\Adminhtml\Report\Shopcart
{
    /**
     * Export abandoned carts report to Excel XML format
     *
     * @return ResponseInterface
     */
    public function execute()
    {
        $fileName = 'shopcart_abandoned.xml';
        $content = $this->_view->getLayout()->createBlock(
            'Magento\Reports\Block\Adminhtml\Shopcart\Abandoned\Grid'
        )->getExcelFile(
            $fileName
        );

        return $this->_fileFactory->create($fileName, $content, \Magento\Framework\App\Filesystem::VAR_DIR);
    }
}
