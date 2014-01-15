<?php
/**
 * Admihtml DirtectPost Payment Controller
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Authorizenet\Controller\Adminhtml\Authorizenet\Directpost;

class Payment
    extends \Magento\Sales\Controller\Adminhtml\Order\Create
{
    /**
     * Core registry
     *
     * @var \Magento\Core\Model\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Catalog\Helper\Product $productHelper
     * @param \Magento\Core\Model\Registry $coreRegistry
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Catalog\Helper\Product $productHelper,
        \Magento\Core\Model\Registry $coreRegistry
    ) {
        $this->_coreRegistry = $coreRegistry;
        parent::__construct($context, $productHelper);
    }

    /**
     * Get session model
     *
     * @return \Magento\Authorizenet\Model\Directpost\Session
     */
    protected function _getDirectPostSession()
    {
        return $this->_objectManager->get('Magento\Authorizenet\Model\Directpost\Session');
    }

    /**
     * Retrieve session object
     *
     * @return \Magento\Backend\Model\Session\Quote
     */
    protected function _getOrderSession()
    {
        return $this->_objectManager->get('Magento\Backend\Model\Session\Quote');
    }

    /**
     * Retrieve order create model
     *
     * @return \Magento\Sales\Model\AdminOrder\Create
     */
    protected function _getOrderCreateModel()
    {
        return $this->_objectManager->get('Magento\Sales\Model\AdminOrder\Create');
    }

    /**
     * Send request to authorize.net
     *
     */
    public function placeAction()
    {
        $paymentParam = $this->getRequest()->getParam('payment');
        $controller = $this->getRequest()->getParam('controller');
        $this->getRequest()->setPost('collect_shipping_rates', 1);
        $this->_processActionData('save');

        //get confirmation by email flag
        $orderData = $this->getRequest()->getPost('order');
        $sendConfirmationFlag = 0;
        if ($orderData) {
            $sendConfirmationFlag = (!empty($orderData['send_confirmation'])) ? 1 : 0;
        } else {
            $orderData = array();
        }

        if (isset($paymentParam['method'])) {
            $result = array();
            $params = $this->_objectManager->get('Magento\Authorizenet\Helper\Data')
                ->getSaveOrderUrlParams($controller);
            //create order partially
            $this->_getOrderCreateModel()->setPaymentData($paymentParam);
            $this->_getOrderCreateModel()->getQuote()->getPayment()->addData($paymentParam);

            $orderData['send_confirmation'] = 0;
            $this->getRequest()->setPost('order', $orderData);

            try {
                //do not cancel old order.
                $oldOrder = $this->_getOrderCreateModel()->getSession()->getOrder();
                $oldOrder->setActionFlag(\Magento\Sales\Model\Order::ACTION_FLAG_CANCEL, false);

                $order = $this->_getOrderCreateModel()
                    ->setIsValidate(true)
                    ->importPostData($this->getRequest()->getPost('order'))
                    ->createOrder();

                $payment = $order->getPayment();
                if ($payment
                    && $payment->getMethod()
                        == $this->_objectManager->create('Magento\Authorizenet\Model\Directpost')->getCode()
                ) {
                    //return json with data.
                    $session = $this->_getDirectPostSession();
                    $session->addCheckoutOrderIncrementId($order->getIncrementId());
                    $session->setLastOrderIncrementId($order->getIncrementId());

                    $requestToPaygate = $payment->getMethodInstance()->generateRequestFromOrder($order);
                    $requestToPaygate->setControllerActionName($controller);
                    $requestToPaygate->setOrderSendConfirmation($sendConfirmationFlag);
                    $requestToPaygate->setStoreId($this->_getOrderCreateModel()->getQuote()->getStoreId());

                    $adminUrl = $this->_objectManager->get('Magento\Backend\Model\Url');
                    if ($adminUrl->useSecretKey()) {
                        $requestToPaygate->setKey(
                            $adminUrl->getSecretKey('adminhtml', 'authorizenet_directpost_payment', 'redirect')
                        );
                    }
                    $result['directpost'] = array('fields' => $requestToPaygate->getData());
                }

                $result['success'] = 1;
                $isError = false;
            } catch (\Magento\Core\Exception $e) {
                $message = $e->getMessage();
                if (!empty($message)) {
                    $this->messageManager->addError($message);
                }
                $isError = true;
            } catch (\Exception $e) {
                $this->messageManager->addException($e, __('Order saving error: %1', $e->getMessage()));
                $isError = true;
            }

            if ($isError) {
                $result['success'] = 0;
                $result['error'] = 1;
                $result['redirect'] = $this->_objectManager
                    ->get('Magento\Backend\Model\Url')
                    ->getUrl('sales/order_create/');
            }

            $this->getResponse()->setBody($this->_objectManager->get('Magento\Core\Helper\Data')->jsonEncode($result));
        }
        else {
            $result = array(
                'error_messages' => __('Please choose a payment method.')
            );
            $this->getResponse()->setBody($this->_objectManager->get('Magento\Core\Helper\Data')->jsonEncode($result));
        }
    }

    /**
     * Retrieve params and put javascript into iframe
     *
     */
    public function redirectAction()
    {
        $redirectParams = $this->getRequest()->getParams();
        $params = array();
        if (!empty($redirectParams['success'])
            && isset($redirectParams['x_invoice_num'])
            && isset($redirectParams['controller_action_name'])
        ) {
            $params['redirect_parent'] = $this->_objectManager->get('Magento\Authorizenet\Helper\HelperInterface')
                ->getSuccessOrderUrl($redirectParams);
            $this->_getDirectPostSession()->unsetData('quote_id');
            //cancel old order
            $oldOrder = $this->_getOrderCreateModel()->getSession()->getOrder();
            if ($oldOrder->getId()) {
                /* @var $order \Magento\Sales\Model\Order */
                $order = $this->_objectManager->create('Magento\Sales\Model\Order')
                    ->loadByIncrementId($redirectParams['x_invoice_num']);
                if ($order->getId()) {
                    $oldOrder->cancel()
                        ->save();
                    $order->save();
                    $this->_getOrderCreateModel()->getSession()->unsOrderId();
                }
            }
            //clear sessions
            $this->_getSession()->clearStorage();
            $this->_getDirectPostSession()->removeCheckoutOrderIncrementId($redirectParams['x_invoice_num']);
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

    /**
     * Return order quote by ajax
     *
     */
    public function returnQuoteAction()
    {
        $this->_returnQuote();
        $this->getResponse()->setBody($this->_objectManager->get('Magento\Core\Helper\Data')
            ->jsonEncode(array('success' => 1)));
    }

    /**
     * Return quote
     *
     * @param bool $cancelOrder
     * @param string $errorMsg
     */
    protected function _returnQuote($cancelOrder = false, $errorMsg = '')
    {
        $incrementId = $this->_getDirectPostSession()->getLastOrderIncrementId();
        if ($incrementId && $this->_getDirectPostSession()->isCheckoutOrderIncrementIdExist($incrementId)) {
            /* @var $order \Magento\Sales\Model\Order */
            $order = $this->_objectManager->create('Magento\Sales\Model\Order')->loadByIncrementId($incrementId);
            if ($order->getId()) {
                $this->_getDirectPostSession()->removeCheckoutOrderIncrementId($order->getIncrementId());
                if ($cancelOrder && $order->getState() == \Magento\Sales\Model\Order::STATE_PENDING_PAYMENT) {
                    $order->registerCancellation($errorMsg)->save();
                }
            }
        }
    }
}
