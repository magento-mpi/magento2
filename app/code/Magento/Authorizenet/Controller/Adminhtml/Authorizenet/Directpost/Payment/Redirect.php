<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Authorizenet\Controller\Adminhtml\Authorizenet\Directpost\Payment;

class Redirect extends \Magento\Sales\Controller\Adminhtml\Order\Create
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Catalog\Helper\Product $productHelper
     * @param \Magento\Framework\Registry $coreRegistry
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Catalog\Helper\Product $productHelper,
        \Magento\Framework\Registry $coreRegistry
    ) {
        $this->_coreRegistry = $coreRegistry;
        parent::__construct($context, $productHelper);
    }

    /**
     * Return quote
     *
     * @param bool $cancelOrder
     * @param string $errorMsg
     * @return void
     */
    protected function _returnQuote($cancelOrder, $errorMsg)
    {
        $directpostSession = $this->_objectManager->get('Magento\Authorizenet\Model\Directpost\Session');
        $incrementId = $directpostSession->getLastOrderIncrementId();
        if ($incrementId && $directpostSession->isCheckoutOrderIncrementIdExist($incrementId)) {
            /* @var $order \Magento\Sales\Model\Order */
            $order = $this->_objectManager->create('Magento\Sales\Model\Order')->loadByIncrementId($incrementId);
            if ($order->getId()) {
                $directpostSession->removeCheckoutOrderIncrementId($order->getIncrementId());
                if ($cancelOrder && $order->getState() == \Magento\Sales\Model\Order::STATE_PENDING_PAYMENT) {
                    $order->registerCancellation($errorMsg)->save();
                }
            }
        }
    }

    /**
     * Retrieve params and put javascript into iframe
     *
     * @return void
     */
    public function execute()
    {
        $redirectParams = $this->getRequest()->getParams();
        $params = array();
        if (!empty($redirectParams['success']) && isset(
            $redirectParams['x_invoice_num']
        ) && isset(
            $redirectParams['controller_action_name']
        )
        ) {
            $params['redirect_parent'] = $this->_objectManager->get(
                'Magento\Authorizenet\Helper\HelperInterface'
            )->getSuccessOrderUrl(
                $redirectParams
            );
            $directpostSession = $this->_objectManager->get('Magento\Authorizenet\Model\Directpost\Session');
            $directpostSession->unsetData('quote_id');
            //cancel old order
            $oldOrder = $this->_getOrderCreateModel()->getSession()->getOrder();
            if ($oldOrder->getId()) {
                /* @var $order \Magento\Sales\Model\Order */
                $order = $this->_objectManager->create(
                    'Magento\Sales\Model\Order'
                )->loadByIncrementId(
                    $redirectParams['x_invoice_num']
                );
                if ($order->getId()) {
                    $oldOrder->cancel()->save();
                    $order->save();
                    $this->_getOrderCreateModel()->getSession()->unsOrderId();
                }
            }
            //clear sessions
            $this->_getSession()->clearStorage();
            $directpostSession->removeCheckoutOrderIncrementId($redirectParams['x_invoice_num']);
            $this->_objectManager->get('Magento\Backend\Model\Session')->clearStorage();
            $this->messageManager->addSuccess(__('You created the order.'));
        }

        if (!empty($redirectParams['error_msg'])) {
            $cancelOrder = empty($redirectParams['x_invoice_num']);
            $this->_returnQuote($cancelOrder, $redirectParams['error_msg']);
        }

        $this->_coreRegistry->register('authorizenet_directpost_form_params', array_merge($params, $redirectParams));
        $this->_view->loadLayout(false)->renderLayout();
    }
}
