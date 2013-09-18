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
 * Sales orders controller
 *
 * @category   Magento
 * @package    MAbout This Orderage_Sales
 * @author      Magento Core Team <core@magentocommerce.com>
 */

namespace Magento\Sales\Controller;

class Guest extends \Magento\Sales\Controller\AbstractController
{
    /**
     * Try to load valid order and register it
     *
     * @param int $orderId
     * @return bool
     */
    protected function _loadValidOrder($orderId = null)
    {
        return $this->_objectManager->get('Magento\Sales\Helper\Guest')->loadValidOrder();
    }

    /**
     * Check order view availability
     *
     * @param   \Magento\Sales\Model\Order $order
     * @return  bool
     */
    protected function _canViewOrder($order)
    {
        $currentOrder = $this->_coreRegistry->registry('current_order');
        if ($order->getId() && ($order->getId() === $currentOrder->getId())) {
            return true;
        }
        return false;
    }

    protected function _viewAction()
    {
        if (!$this->_loadValidOrder()) {
            return;
        }

        $this->loadLayout();
        $this->_objectManager->get('Magento\Sales\Helper\Guest')->getBreadcrumbs($this);
        $this->renderLayout();
    }

    /**
     * Order view form page
     */
    public function formAction()
    {
        if (\Mage::getSingleton('Magento\Customer\Model\Session')->isLoggedIn()) {
            $this->_redirect('customer/account/');
            return;
        }
        $this->loadLayout();
        $this->getLayout()->getBlock('head')->setTitle(__('Orders and Returns'));
        $this->_objectManager->get('Magento\Sales\Helper\Guest')->getBreadcrumbs($this);
        $this->renderLayout();
    }

    public function printInvoiceAction()
    {
        if (!$this->_loadValidOrder()) {
            return;
        }

        $invoiceId = (int) $this->getRequest()->getParam('invoice_id');
        if ($invoiceId) {
            $invoice = \Mage::getModel('Magento\Sales\Model\Order\Invoice')->load($invoiceId);
            $order = $invoice->getOrder();
        } else {
            $order = $this->_coreRegistry->registry('current_order');
        }

        if ($this->_canViewOrder($order)) {
            if (isset($invoice)) {
                $this->_coreRegistry->register('current_invoice', $invoice);
            }
            $this->loadLayout('print');
            $this->renderLayout();
        } else {
            $this->_redirect('sales/guest/form');
        }
    }

    public function printShipmentAction()
    {
        if (!$this->_loadValidOrder()) {
            return;
        }

        $shipmentId = (int) $this->getRequest()->getParam('shipment_id');
        if ($shipmentId) {
            $shipment = \Mage::getModel('Magento\Sales\Model\Order\Shipment')->load($shipmentId);
            $order = $shipment->getOrder();
        } else {
            $order = $this->_coreRegistry->registry('current_order');
        }
        if ($this->_canViewOrder($order)) {
            if (isset($shipment)) {
                $this->_coreRegistry->register('current_shipment', $shipment);
            }
            $this->loadLayout('print');
            $this->renderLayout();
        } else {
            $this->_redirect('sales/guest/form');
        }
    }

    public function printCreditmemoAction()
    {
        if (!$this->_loadValidOrder()) {
            return;
        }

        $creditmemoId = (int) $this->getRequest()->getParam('creditmemo_id');
        if ($creditmemoId) {
            $creditmemo = \Mage::getModel('Magento\Sales\Model\Order\Creditmemo')->load($creditmemoId);
            $order = $creditmemo->getOrder();
        } else {
            $order = $this->_coreRegistry->registry('current_order');
        }

        if ($this->_canViewOrder($order)) {
            if (isset($creditmemo)) {
                $this->_coreRegistry->register('current_creditmemo', $creditmemo);
            }
            $this->loadLayout('print');
            $this->renderLayout();
        } else {
            $this->_redirect('sales/guest/form');
        }
    }
}
