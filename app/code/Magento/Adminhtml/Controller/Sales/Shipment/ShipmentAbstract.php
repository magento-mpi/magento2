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

    public function pdfshipmentsAction(){
        $shipmentIds = $this->getRequest()->getPost('shipment_ids');
        if (!empty($shipmentIds)) {
            $shipments = \Mage::getResourceModel('Magento\Sales\Model\Resource\Order\Shipment\Collection')
                ->addAttributeToSelect('*')
                ->addAttributeToFilter('entity_id', array('in' => $shipmentIds))
                ->load();
            if (!isset($pdf)){
                $pdf = \Mage::getModel('Magento\Sales\Model\Order\Pdf\Shipment')->getPdf($shipments);
            } else {
                $pages = \Mage::getModel('Magento\Sales\Model\Order\Pdf\Shipment')->getPdf($shipments);
                $pdf->pages = array_merge ($pdf->pages, $pages->pages);
            }

            return $this->_prepareDownloadResponse('packingslip'
                    . \Mage::getSingleton('Magento\Core\Model\Date')->date('Y-m-d_H-i-s')
                    . '.pdf', $pdf->render(), 'application/pdf');
        }
        $this->_redirect('*/*/');
    }


    public function printAction()
    {
        /** @see \Magento\Adminhtml\Controller\Sales\Order\Invoice */
        if ($shipmentId = $this->getRequest()->getParam('invoice_id')) { // invoice_id o_0
            if ($shipment = \Mage::getModel('Magento\Sales\Model\Order\Shipment')->load($shipmentId)) {
                $pdf = \Mage::getModel('Magento\Sales\Model\Order\Pdf\Shipment')->getPdf(array($shipment));
                $this->_prepareDownloadResponse('packingslip'
                        . \Mage::getSingleton('Magento\Core\Model\Date')->date('Y-m-d_H-i-s')
                        . '.pdf', $pdf->render(), 'application/pdf');
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
