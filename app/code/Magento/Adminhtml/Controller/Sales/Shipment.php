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
class Magento_Adminhtml_Controller_Sales_Shipment extends Magento_Adminhtml_Controller_Sales_Shipment_ShipmentAbstract
{
    /**
     * Export shipment grid to CSV format
     */
    public function exportCsvAction()
    {
        $fileName   = 'shipments.csv';
        $grid       = $this->getLayout()->createBlock('Magento_Adminhtml_Block_Sales_Shipment_Grid');
        $this->_prepareDownloadResponse($fileName, $grid->getCsvFile());
    }

    /**
     *  Export shipment grid to Excel XML format
     */
    public function exportExcelAction()
    {
        $fileName   = 'shipments.xml';
        $grid       = $this->getLayout()->createBlock('Magento_Adminhtml_Block_Sales_Shipment_Grid');
        $this->_prepareDownloadResponse($fileName, $grid->getExcelFile($fileName));
    }
}
