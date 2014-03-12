<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Controller\Adminhtml;

use Magento\App\ResponseInterface;

/**
 * Adminhtml sales orders controller
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Invoice extends \Magento\Sales\Controller\Adminhtml\Invoice\AbstractInvoice
{
    /**
     * Export invoice grid to CSV format
     *
     * @return ResponseInterface
     */
    public function exportCsvAction()
    {
        $fileName   = 'invoices.csv';
        $grid       = $this->_view->getLayout()->createBlock('Magento\Sales\Block\Adminhtml\Invoice\Grid');
        return $this->_fileFactory->create($fileName, $grid->getCsvFile());
    }

    /**
     * Export invoice grid to Excel XML format
     *
     * @return ResponseInterface
     */
    public function exportExcelAction()
    {
        $fileName   = 'invoices.xml';
        $grid       = $this->_view->getLayout()->createBlock('Magento\Sales\Block\Adminhtml\Invoice\Grid');
        return $this->_fileFactory->create($fileName, $grid->getExcelFile($fileName));
    }
}
