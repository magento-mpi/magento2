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
class Magento_Adminhtml_Controller_Sales_Shipment_ShipmentAbstract extends Magento_Adminhtml_Controller_Action
{
    /**
     * Init layout, menu and breadcrumb
     *
     * @return Magento_Adminhtml_Controller_Sales_Shipment
     */
    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('Magento_Sales::sales_shipment')
            ->_addBreadcrumb(__('Sales'), __('Sales'))
            ->_addBreadcrumb(__('Shipments'), __('Shipments'));
        return $this;
    }

    /**
     * Shipments grid
     */
    public function indexAction()
    {
        $this->_title(__('Shipments'));

        $this->_initAction()
            ->_addContent($this->getLayout()->createBlock('Magento_Adminhtml_Block_Sales_Shipment'))
            ->renderLayout();
    }

    /**
     * Shipment information page
     */
    public function viewAction()
    {
        if ($shipmentId = $this->getRequest()->getParam('shipment_id')) {
            $this->_forward('view', 'sales_order_shipment', null, array('come_from'=>'shipment'));
        } else {
            $this->_forward('noRoute');
        }
    }

    public function pdfshipmentsAction(){
        $shipmentIds = $this->getRequest()->getPost('shipment_ids');
        if (!empty($shipmentIds)) {
            $shipments = Mage::getResourceModel('Magento_Sales_Model_Resource_Order_Shipment_Collection')
                ->addAttributeToSelect('*')
                ->addAttributeToFilter('entity_id', array('in' => $shipmentIds))
                ->load();
            if (!isset($pdf)){
                $pdf = Mage::getModel('Magento_Sales_Model_Order_Pdf_Shipment')->getPdf($shipments);
            } else {
                $pages = Mage::getModel('Magento_Sales_Model_Order_Pdf_Shipment')->getPdf($shipments);
                $pdf->pages = array_merge ($pdf->pages, $pages->pages);
            }

            return $this->_prepareDownloadResponse('packingslip'.Mage::getSingleton('Magento_Core_Model_Date')->date('Y-m-d_H-i-s').'.pdf', $pdf->render(), 'application/pdf');
        }
        $this->_redirect('*/*/');
    }


    public function printAction()
    {
        /** @see Magento_Adminhtml_Controller_Sales_Order_Invoice */
        if ($shipmentId = $this->getRequest()->getParam('invoice_id')) { // invoice_id o_0
            if ($shipment = Mage::getModel('Magento_Sales_Model_Order_Shipment')->load($shipmentId)) {
                $pdf = Mage::getModel('Magento_Sales_Model_Order_Pdf_Shipment')->getPdf(array($shipment));
                $this->_prepareDownloadResponse('packingslip'.Mage::getSingleton('Magento_Core_Model_Date')->date('Y-m-d_H-i-s').'.pdf', $pdf->render(), 'application/pdf');
            }
        }
        else {
            $this->_forward('noRoute');
        }
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magento_Sales::shipment');
    }
}
