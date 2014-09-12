<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\TaxImportExport\Controller\Adminhtml\Rate;

use \Magento\Framework\App\ResponseInterface;

class ExportXml extends \Magento\TaxImportExport\Controller\Adminhtml\Rate
{
    /**
     * Export rates grid to XML format
     *
     * @return ResponseInterface
     */
    public function execute()
    {
        $this->_view->loadLayout(false);
        $content = $this->_view->getLayout()->getChildBlock('adminhtml.tax.rate.grid', 'grid.export');
        return $this->fileFactory->create(
            'rates.xml',
            $content->getExcelFile(),
            \Magento\Framework\App\Filesystem::VAR_DIR
        );
    }
}
