<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Cms
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Sales Controller
 */
abstract class Magento_Sales_Controller_Abstract extends Magento_Core_Controller_Front_Action
{
    /**
     * Core registry
     *
     * @var Magento_Core_Model_Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param Magento_Core_Controller_Varien_Action_Context $context
     * @param Magento_Core_Model_Registry $coreRegistry
     */
    public function __construct(
        Magento_Core_Controller_Varien_Action_Context $context,
        Magento_Core_Model_Registry $coreRegistry
    ) {
        $this->_coreRegistry = $coreRegistry;
        parent::__construct($context);
    }

    /**
     * Check order view availability
     *
     * @param   Magento_Sales_Model_Order $order
     * @return  bool
     */
    protected function _canViewOrder($order)
    {
        $customerId = $this->_objectManager->get('Magento_Customer_Model_Session')->getCustomerId();
        $availableStates = $this->_objectManager->get('Magento_Sales_Model_Order_Config')->getVisibleOnFrontStates();
        if ($order->getId() && $order->getCustomerId() && ($order->getCustomerId() == $customerId)
            && in_array($order->getState(), $availableStates, true)
        ) {
            return true;
        }
        return false;
    }

    /**
     * Init layout, messages and set active block for customer
     */
    protected function _viewAction()
    {
        if (!$this->_loadValidOrder()) {
            return;
        }

        $this->loadLayout();
        $this->_initLayoutMessages('Magento_Catalog_Model_Session');

        $navigationBlock = $this->getLayout()->getBlock('customer_account_navigation');
        if ($navigationBlock) {
            $navigationBlock->setActive('sales/order/history');
        }
        $this->renderLayout();
    }

    /**
     * Try to load valid order by order_id and register it
     *
     * @param int $orderId
     * @return bool
     */
    protected function _loadValidOrder($orderId = null)
    {
        if (null === $orderId) {
            $orderId = (int) $this->getRequest()->getParam('order_id');
        }
        if (!$orderId) {
            $this->_forward('noRoute');
            return false;
        }

        $order = $this->_objectManager->create('Magento_Sales_Model_Order')->load($orderId);

        if ($this->_canViewOrder($order)) {
            $this->_coreRegistry->register('current_order', $order);
            return true;
        } else {
            $this->_redirect('*/*/history');
        }
        return false;
    }

    /**
     * Order view page
     */
    public function viewAction()
    {
        $this->_viewAction();
    }

    /**
     * Invoice page
     */
    public function invoiceAction()
    {
        $this->_viewAction();
    }

    /**
     * Shipment page
     */
    public function shipmentAction()
    {
        $this->_viewAction();
    }

    /**
     * Creditmemo page
     */
    public function creditmemoAction()
    {
        $this->_viewAction();
    }

    /**
     * Action for reorder
     */
    public function reorderAction()
    {
        if (!$this->_loadValidOrder()) {
            return;
        }
        $order = $this->_coreRegistry->registry('current_order');

        /* @var $cart Magento_Checkout_Model_Cart */
        $cart = $this->_objectManager->get('Magento_Checkout_Model_Cart');
        $items = $order->getItemsCollection();
        foreach ($items as $item) {
            try {
                $cart->addOrderItem($item);
            } catch (Magento_Core_Exception $e){
                if ($this->_objectManager->get('Magento_Checkout_Model_Session')->getUseNotice(true)) {
                    $this->_objectManager->get('Magento_Checkout_Model_Session')->addNotice($e->getMessage());
                } else {
                    $this->_objectManager->get('Magento_Checkout_Model_Session')->addError($e->getMessage());
                }
                $this->_redirect('*/*/history');
            } catch (Exception $e) {
                $this->_objectManager->get('Magento_Checkout_Model_Session')->addException($e,
                    __('We cannot add this item to your shopping cart.')
                );
                $this->_redirect('checkout/cart');
            }
        }

        $cart->save();
        $this->_redirect('checkout/cart');
    }

    /**
     * Print Order Action
     */
    public function printAction()
    {
        if (!$this->_loadValidOrder()) {
            return;
        }
        $this->loadLayout('print');
        $this->renderLayout();
    }

    /**
     * Print Invoice Action
     */
    public function printInvoiceAction()
    {
        $invoiceId = (int) $this->getRequest()->getParam('invoice_id');
        if ($invoiceId) {
            $invoice = $this->_objectManager->create('Magento_Sales_Model_Order_Invoice')->load($invoiceId);
            $order = $invoice->getOrder();
        } else {
            $orderId = (int) $this->getRequest()->getParam('order_id');
            $order = $this->_objectManager->create('Magento_Sales_Model_Order')->load($orderId);
        }

        if ($this->_canViewOrder($order)) {
            $this->_coreRegistry->register('current_order', $order);
            if (isset($invoice)) {
                $this->_coreRegistry->register('current_invoice', $invoice);
            }
            $this->loadLayout('print');
            $this->renderLayout();
        } else {
            if ($this->_objectManager->get('Magento_Customer_Model_Session')->isLoggedIn()) {
                $this->_redirect('*/*/history');
            } else {
                $this->_redirect('sales/guest/form');
            }
        }
    }

    /**
     * Print Shipment Action
     */
    public function printShipmentAction()
    {
        $shipmentId = (int) $this->getRequest()->getParam('shipment_id');
        if ($shipmentId) {
            $shipment = $this->_objectManager->create('Magento_Sales_Model_Order_Shipment')->load($shipmentId);
            $order = $shipment->getOrder();
        } else {
            $orderId = (int) $this->getRequest()->getParam('order_id');
            $order = $this->_objectManager->create('Magento_Sales_Model_Order')->load($orderId);
        }
        if ($this->_canViewOrder($order)) {
            $this->_coreRegistry->register('current_order', $order);
            if (isset($shipment)) {
                $this->_coreRegistry->register('current_shipment', $shipment);
            }
            $this->loadLayout('print');
            $this->renderLayout();
        } else {
            if ($this->_objectManager->get('Magento_Customer_Model_Session')->isLoggedIn()) {
                $this->_redirect('*/*/history');
            } else {
                $this->_redirect('sales/guest/form');
            }
        }
    }

    /**
     * Print Creditmemo Action
     */
    public function printCreditmemoAction()
    {
        $creditmemoId = (int)$this->getRequest()->getParam('creditmemo_id');
        if ($creditmemoId) {
            $creditmemo = $this->_objectManager->create('Magento_Sales_Model_Order_Creditmemo')->load($creditmemoId);
            $order = $creditmemo->getOrder();
        } else {
            $orderId = (int)$this->getRequest()->getParam('order_id');
            $order = $this->_objectManager->create('Magento_Sales_Model_Order')->load($orderId);
        }

        if ($this->_canViewOrder($order)) {
            $this->_coreRegistry->register('current_order', $order);
            if (isset($creditmemo)) {
                $this->_coreRegistry->register('current_creditmemo', $creditmemo);
            }
            $this->loadLayout('print');
            $this->renderLayout();
        } else {
            if ($this->_objectManager->get('Magento_Customer_Model_Session')->isLoggedIn()) {
                $this->_redirect('*/*/history');
            } else {
                $this->_redirect('sales/guest/form');
            }
        }
    }
}
