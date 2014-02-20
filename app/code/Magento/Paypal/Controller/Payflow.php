<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Payflow Checkout Controller
 */
namespace Magento\Paypal\Controller;

class Payflow extends \Magento\App\Action\Action
{
    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $_checkoutSession;

    /**
     * @var \Magento\Sales\Model\OrderFactory
     */
    protected $_orderFactory;

    /**
     * @var \Magento\Logger
     */
    protected $_logger;

    /**
     * @var \Magento\Paypal\Model\PayflowlinkFactory
     */
    protected $_payflowlinkFactory;

    /**
     * @var \Magento\Paypal\Helper\Checkout
     */
    protected $_checkoutHelper;

    /**
     * @param \Magento\App\Action\Context $context
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Sales\Model\OrderFactory $orderFactory
     * @param \Magento\Paypal\Model\PayflowlinkFactory $payflowlinkFactory
     * @param \Magento\Paypal\Helper\Checkout $checkoutHelper
     */
    public function __construct(
        \Magento\App\Action\Context $context,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Sales\Model\OrderFactory $orderFactory,
        \Magento\Paypal\Model\PayflowlinkFactory $payflowlinkFactory,
        \Magento\Paypal\Helper\Checkout $checkoutHelper,
        \Magento\Logger $logger
    ) {
        $this->_checkoutSession = $checkoutSession;
        $this->_orderFactory = $orderFactory;
        $this->_logger = $logger;
        $this->_payflowlinkFactory = $payflowlinkFactory;
        $this->_checkoutHelper = $checkoutHelper;
        parent::__construct($context);
    }

    /**
     * When a customer cancel payment from payflow gateway.
     */
    public function cancelPaymentAction()
    {
        $this->_view->loadLayout(false);
        $gotoSection = $this->_cancelPayment();
        $redirectBlock = $this->_view->getLayout()->getBlock('payflow.link.iframe');
        $redirectBlock->setGotoSection($gotoSection);
        $this->_view->renderLayout();
    }

    /**
     * When a customer return to website from payflow gateway.
     */
    public function returnUrlAction()
    {
        $this->_view->loadLayout(false);
        $redirectBlock = $this->_view->getLayout()->getBlock('payflow.link.iframe');

        if ($this->_checkoutSession->getLastRealOrderId()) {
            $order = $this->_orderFactory->create()->loadByIncrementId($this->_checkoutSession->getLastRealOrderId());

            if ($order && $order->getIncrementId() == $this->_checkoutSession->getLastRealOrderId()) {
                $allowedOrderStates = array(
                    \Magento\Sales\Model\Order::STATE_PROCESSING,
                    \Magento\Sales\Model\Order::STATE_COMPLETE
                );
                if (in_array($order->getState(), $allowedOrderStates)) {
                    $this->_checkoutSession->unsLastRealOrderId();
                    $redirectBlock->setGotoSuccessPage(true);
                } else {
                    $gotoSection = $this->_cancelPayment(strval($this->getRequest()->getParam('RESPMSG')));
                    $redirectBlock->setGotoSection($gotoSection);
                    $redirectBlock->setErrorMsg(__('Your payment has been declined. Please try again.'));
                }
            }
        }

        $this->_view->renderLayout();
    }

    /**
     * Submit transaction to Payflow getaway into iframe
     */
    public function formAction()
    {
        $this->_view->loadLayout(false)->renderLayout();
    }

    /**
     * Get response from PayPal by silent post method
     */
    public function silentPostAction()
    {
        $data = $this->getRequest()->getPost();
        if (isset($data['INVNUM'])) {
            /** @var $paymentModel \Magento\Paypal\Model\Payflowlink */
            $paymentModel = $this->_payflowlinkFactory->create();
            try {
                $paymentModel->process($data);
            } catch (\Exception $e) {
                $this->_logger->logException($e);
            }
        }
    }

    /**
     * Cancel order, return quote to customer
     *
     * @param string $errorMsg
     * @return mixed
     */
    protected function _cancelPayment($errorMsg = '')
    {
        $gotoSection = false;
        $this->_checkoutHelper->cancelCurrentOrder($errorMsg);
        if ($this->_checkoutSession->restoreQuote()) {
            //Redirect to payment step
            $gotoSection = 'payment';
        }

        return $gotoSection;
    }
}
