<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml sales orders controller
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Adminhtml\Controller\Sales;

class Invoice extends \Magento\Adminhtml\Controller\Sales\Invoice\InvoiceAbstract
{
    /**
     * Export invoice grid to CSV format
     */
    public function exportCsvAction()
    {
        $fileName   = 'invoices.csv';
        $grid       = $this->getLayout()->createBlock('Magento\Adminhtml\Block\Sales\Invoice\Grid');
        $this->_prepareDownloadResponse($fileName, $grid->getCsvFile());
    }

    /**
     *  Export invoice grid to Excel XML format
     */
    public function exportExcelAction()
    {
        $fileName   = 'invoices.xml';
        $grid       = $this->getLayout()->createBlock('Magento\Adminhtml\Block\Sales\Invoice\Grid');
        $this->_prepareDownloadResponse($fileName, $grid->getExcelFile($fileName));
    }
}
