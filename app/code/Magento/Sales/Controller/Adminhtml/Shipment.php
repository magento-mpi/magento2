<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml sales orders controller
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Sales\Controller\Adminhtml;

class Shipment extends \Magento\Sales\Controller\Adminhtml\Shipment\AbstractShipment
{
    /**
     * Export shipment grid to CSV format
     */
    public function exportCsvAction()
    {
        $fileName   = 'shipments.csv';
        $grid       = $this->getLayout()->createBlock('Magento\Sales\Block\Adminhtml\Shipment\Grid');
        return $this->_fileFactory->create($fileName, $grid->getCsvFile());
    }

    /**
     *  Export shipment grid to Excel XML format
     */
    public function exportExcelAction()
    {
        $fileName   = 'shipments.xml';
        $grid       = $this->getLayout()->createBlock('Magento\Sales\Block\Adminhtml\Shipment\Grid');
        return $this->_fileFactory->create($fileName, $grid->getExcelFile($fileName));
    }
}
