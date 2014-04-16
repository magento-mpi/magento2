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
        $this->_view->loadLayout(false);
        $fileName = 'shipments.csv';
        $grid = $this->_view->getLayout()->getChildBlock('sales.shipment.grid', 'grid.export');
        return $this->_fileFactory->create($fileName, $grid->getCsvFile(), \Magento\App\Filesystem::VAR_DIR);
    }

    /**
     * Export shipment grid to Excel XML format
     *
     * @return ResponseInterface
     */
    public function exportExcelAction()
    {
        $this->_view->loadLayout(false);
        $fileName = 'shipments.xml';
        $grid = $this->_view->getLayout()->getChildBlock('sales.shipment.grid', 'grid.export');
        return $this->_fileFactory->create($fileName, $grid->getExcelFile($fileName), \Magento\App\Filesystem::VAR_DIR);
    }
}
