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
class Shipment extends \Magento\Sales\Controller\Adminhtml\Shipment\AbstractShipment
{
    /**
     * Export shipment grid to CSV format
     *
     * @return ResponseInterface
     */
    public function exportCsvAction()
    {
        $fileName   = 'shipments.csv';
        $grid       = $this->_view->getLayout()->createBlock('Magento\Sales\Block\Adminhtml\Shipment\Grid');
        return $this->_fileFactory->create($fileName, $grid->getCsvFile());
    }

    /**
     * Export shipment grid to Excel XML format
     *
     * @return ResponseInterface
     */
    public function exportExcelAction()
    {
        $fileName   = 'shipments.xml';
        $grid       = $this->_view->getLayout()->createBlock('Magento\Sales\Block\Adminhtml\Shipment\Grid');
        return $this->_fileFactory->create($fileName, $grid->getExcelFile($fileName));
    }
}
