<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Cms
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Controller;

/**
 * Sales Controller
 */
abstract class AbstractController extends \Magento\App\Action\Action
{
    /**
     * Core registry
     *
     * @var \Magento\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param \Magento\App\Action\Context $context
     * @param \Magento\Registry $coreRegistry
     */
    public function __construct(
        \Magento\App\Action\Context $context,
        \Magento\Registry $coreRegistry
    ) {
        $this->_coreRegistry = $coreRegistry;
        parent::__construct($context);
    }

    /**
     * Check order view availability
     *
     * @param   \Magento\Sales\Model\Order $order
     * @return  bool
     */
    protected function _canViewOrder($order)
    {
        $customerId = $this->_objectManager->get('Magento\Customer\Model\Session')->getCustomerId();
        $availableStates = $this->_objectManager->get('Magento\Sales\Model\Order\Config')->getVisibleOnFrontStates();
        if ($order->getId() && $order->getCustomerId() && ($order->getCustomerId() == $customerId)
            && in_array($order->getState(), $availableStates, true)
        ) {
            return true;
        }
        return false;
    }

    /**
     * Init layout, messages and set active block for customer
     *
     * @return void
     */
    protected function _viewAction()
    {
        if (!$this->_loadValidOrder()) {
            return;
        }

        $this->_view->loadLayout();
        $this->_view->getLayout()->initMessages();

        $navigationBlock = $this->_view->getLayout()->getBlock('customer_account_navigation');
        if ($navigationBlock) {
            $navigationBlock->setActive('sales/order/history');
        }
        $this->_view->renderLayout();
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
            $this->_forward('noroute');
            return false;
        }

        $order = $this->_objectManager->create('Magento\Sales\Model\Order')->load($orderId);

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
     *
     * @return void
     */
    public function viewAction()
    {
        $this->_viewAction();
    }

    /**
     * Invoice page
     *
     * @return void
     */
    public function invoiceAction()
    {
        $this->_viewAction();
    }

    /**
     * Shipment page
     *
     * @return void
     */
    public function shipmentAction()
    {
        $this->_viewAction();
    }

    /**
     * Creditmemo page
     *
     * @return void
     */
    public function creditmemoAction()
    {
        $this->_viewAction();
    }

    /**
     * Action for reorder
     *
     * @return void
     */
    public function reorderAction()
    {
        if (!$this->_loadValidOrder()) {
            return;
        }
        $order = $this->_coreRegistry->registry('current_order');

        /* @var $cart \Magento\Checkout\Model\Cart */
        $cart = $this->_objectManager->get('Magento\Checkout\Model\Cart');
        $items = $order->getItemsCollection();
        foreach ($items as $item) {
            try {
                $cart->addOrderItem($item);
            } catch (\Magento\Core\Exception $e) {
                if ($this->_objectManager->get('Magento\Checkout\Model\Session')->getUseNotice(true)) {
                    $this->messageManager->addNotice($e->getMessage());
                } else {
                    $this->messageManager->addError($e->getMessage());
                }
                $this->_redirect('*/*/history');
            } catch (\Exception $e) {
                $this->messageManager->addException($e, __('We cannot add this item to your shopping cart.'));
                $this->_redirect('checkout/cart');
            }
        }

        $cart->save();
        $this->_redirect('checkout/cart');
    }

    /**
     * Print Order Action
     *
     * @return void
     */
    public function printAction()
    {
        if (!$this->_loadValidOrder()) {
            return;
        }
        $this->_view->loadLayout('print');
        $this->_view->renderLayout();
    }

    /**
     * Print Invoice Action
     *
     * @return void
     */
    public function printInvoiceAction()
    {
        $invoiceId = (int) $this->getRequest()->getParam('invoice_id');
        if ($invoiceId) {
            $invoice = $this->_objectManager->create('Magento\Sales\Model\Order\Invoice')->load($invoiceId);
            $order = $invoice->getOrder();
        } else {
            $orderId = (int) $this->getRequest()->getParam('order_id');
            $order = $this->_objectManager->create('Magento\Sales\Model\Order')->load($orderId);
        }

        if ($this->_canViewOrder($order)) {
            $this->_coreRegistry->register('current_order', $order);
            if (isset($invoice)) {
                $this->_coreRegistry->register('current_invoice', $invoice);
            }
            $this->_view->loadLayout('print');
            $this->_view->renderLayout();
        } else {
            if ($this->_objectManager->get('Magento\Customer\Model\Session')->isLoggedIn()) {
                $this->_redirect('*/*/history');
            } else {
                $this->_redirect('sales/guest/form');
            }
        }
    }

    /**
     * Print Shipment Action
     *
     * @return void
     */
    public function printShipmentAction()
    {
        $shipmentId = (int) $this->getRequest()->getParam('shipment_id');
        if ($shipmentId) {
            $shipment = $this->_objectManager->create('Magento\Sales\Model\Order\Shipment')->load($shipmentId);
            $order = $shipment->getOrder();
        } else {
            $orderId = (int) $this->getRequest()->getParam('order_id');
            $order = $this->_objectManager->create('Magento\Sales\Model\Order')->load($orderId);
        }
        if ($this->_canViewOrder($order)) {
            $this->_coreRegistry->register('current_order', $order);
            if (isset($shipment)) {
                $this->_coreRegistry->register('current_shipment', $shipment);
            }
            $this->_view->loadLayout('print');
            $this->_view->renderLayout();
        } else {
            if ($this->_objectManager->get('Magento\Customer\Model\Session')->isLoggedIn()) {
                $this->_redirect('*/*/history');
            } else {
                $this->_redirect('sales/guest/form');
            }
        }
    }

    /**
     * Print Creditmemo Action
     *
     * @return void
     */
    public function printCreditmemoAction()
    {
        $creditmemoId = (int)$this->getRequest()->getParam('creditmemo_id');
        if ($creditmemoId) {
            $creditmemo = $this->_objectManager->create('Magento\Sales\Model\Order\Creditmemo')->load($creditmemoId);
            $order = $creditmemo->getOrder();
        } else {
            $orderId = (int)$this->getRequest()->getParam('order_id');
            $order = $this->_objectManager->create('Magento\Sales\Model\Order')->load($orderId);
        }

        if ($this->_canViewOrder($order)) {
            $this->_coreRegistry->register('current_order', $order);
            if (isset($creditmemo)) {
                $this->_coreRegistry->register('current_creditmemo', $creditmemo);
            }
            $this->_view->loadLayout('print');
            $this->_view->renderLayout();
        } else {
            if ($this->_objectManager->get('Magento\Customer\Model\Session')->isLoggedIn()) {
                $this->_redirect('*/*/history');
            } else {
                $this->_redirect('sales/guest/form');
            }
        }
    }
}
