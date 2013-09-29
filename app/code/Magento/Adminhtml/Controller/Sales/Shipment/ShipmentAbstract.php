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
namespace Magento\Adminhtml\Controller\Sales\Shipment;

class ShipmentAbstract extends \Magento\Adminhtml\Controller\Action
{
    /**
     * Init layout, menu and breadcrumb
     *
     * @return \Magento\Adminhtml\Controller\Sales\Shipment
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
            ->_addContent($this->getLayout()->createBlock('Magento\Adminhtml\Block\Sales\Shipment'))
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

    public function pdfshipmentsAction()
    {
        $shipmentIds = $this->getRequest()->getPost('shipment_ids');
        if (!empty($shipmentIds)) {
            $shipments = $this->_objectManager->create('Magento\Sales\Model\Resource\Order\Shipment\Collection')
                ->addAttributeToSelect('*')
                ->addAttributeToFilter('entity_id', array('in' => $shipmentIds))
                ->load();
            if (!isset($pdf)) {
                $pdf = $this->_objectManager->create('Magento\Sales\Model\Order\Pdf\Shipment')->getPdf($shipments);
            } else {
                $pages = $this->_objectManager->create('Magento\Sales\Model\Order\Pdf\Shipment')->getPdf($shipments);
                $pdf->pages = array_merge ($pdf->pages, $pages->pages);
            }
            $date = $this->_objectManager->get('Magento\Core\Model\Date')->date('Y-m-d_H-i-s');
            return $this->_prepareDownloadResponse('packingslip' . $date . '.pdf', $pdf->render(), 'application/pdf');
        }
        $this->_redirect('*/*/');
    }

    public function printAction()
    {
        /** @see \Magento\Adminhtml\Controller\Sales\Order\Invoice */
        $shipmentId = $this->getRequest()->getParam('invoice_id');
        if ($shipmentId) { // invoice_id o_0
            $shipment = $this->_objectManager->create('Magento\Sales\Model\Order\Shipment')->load($shipmentId);
            if ($shipment) {
                $pdf = $this->_objectManager->create('Magento\Sales\Model\Order\Pdf\Shipment')
                    ->getPdf(array($shipment));
                $date = $this->_objectManager->get('Magento\Core\Model\Date')->date('Y-m-d_H-i-s');
                $this->_prepareDownloadResponse('packingslip' . $date . '.pdf', $pdf->render(), 'application/pdf');
            }
        } else {
            $this->_forward('noRoute');
        }
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magento_Sales::shipment');
    }
}
